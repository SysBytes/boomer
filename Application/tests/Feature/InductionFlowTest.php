<?php

namespace Tests\Feature;

use App\Models\CorporateContent;
use App\Models\DocumentCategory;
use App\Models\DocumentSubcategory;
use App\Models\Role;
use App\Models\User;
use App\Models\UserContentProgress;
use App\Models\UserInductionEvidence;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InductionFlowTest extends TestCase
{
    use DatabaseTransactions;

    private function makeInductionFixture(): array
    {
        Storage::fake('local');
        Storage::fake('public');

        $suffix = bin2hex(random_bytes(4));
        $role = Role::create(['slug' => 'ind-role-' . $suffix, 'name' => 'Rol Inducción ' . $suffix]);

        $category = DocumentCategory::create(['name' => 'Inducción Obligatoria']);
        $subcategory = DocumentSubcategory::create([
            'category_id' => $category->id,
            'name' => 'Normativa',
        ]);

        Storage::disk('local')->put(
            'induction/pdf/paso1.pdf',
            "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF"
        );

        $step1 = CorporateContent::create([
            'subcategory_id' => $subcategory->id,
            'title' => 'Paso 1: Manual PDF',
            'type' => 'pdf',
            'file_path' => 'induction/pdf/paso1.pdf',
            'required_time_seconds' => 30,
            'target_role_id' => $role->id,
            'is_mandatory' => true,
            'is_active' => true,
        ]);
        $step1->roles()->sync([$role->id]);

        $step2 = CorporateContent::create([
            'subcategory_id' => $subcategory->id,
            'title' => 'Paso 2: Video YouTube',
            'type' => 'video',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'required_time_seconds' => 10,
            'target_role_id' => $role->id,
            'is_mandatory' => true,
            'is_active' => true,
        ]);
        $step2->roles()->sync([$role->id]);

        $optionalContent = CorporateContent::create([
            'subcategory_id' => $subcategory->id,
            'title' => 'Material Opcional',
            'type' => 'video',
            'url' => 'https://vimeo.com/123456789',
            'required_time_seconds' => 60,
            'target_role_id' => $role->id,
            'is_mandatory' => false,
            'is_active' => true,
        ]);
        $optionalContent->roles()->sync([$role->id]);

        $pendingUser = User::factory()->create([
            'active' => 1,
            'induction_status' => 'pending',
        ]);
        $pendingUser->roles()->attach($role->id);

        $completedUser = User::factory()->create([
            'active' => 1,
            'induction_status' => 'completed',
        ]);
        $completedUser->roles()->attach($role->id);

        return compact('role', 'step1', 'step2', 'optionalContent', 'pendingUser', 'completedUser');
    }

    public function test_middleware_redirects_pending_user_to_induction_and_allows_induction_and_logout(): void
    {
        $f = $this->makeInductionFixture();

        $this->actingAs($f['pendingUser'])
            ->get(route('dashboard'))
            ->assertRedirect(route('induction.index'));

        $this->actingAs($f['pendingUser'])
            ->get(route('dashboard.corporate-contents.index'))
            ->assertRedirect(route('induction.index'));

        $this->actingAs($f['pendingUser'])
            ->getJson(route('dashboard.corporate-contents.index'))
            ->assertStatus(403)
            ->assertJson(['redirect_url' => route('induction.index')]);

        $this->actingAs($f['pendingUser'])
            ->get(route('induction.index'))
            ->assertOk()
            ->assertSee('id="page-header"', false)
            ->assertSee('id="logout-form"', false)
            ->assertSeeInOrder(['inductionViewerContainer', 'Marcar como completado y continuar'])
            ->assertSee('Paso 1: Manual PDF')
            ->assertDontSee('Material Opcional');

        $this->actingAs($f['pendingUser'])
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->actingAs($f['completedUser'])
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_sequential_order_prevents_skipping_to_step_two(): void
    {
        $f = $this->makeInductionFixture();

        $this->actingAs($f['pendingUser'])
            ->postJson(route('induction.heartbeat', $f['step2']->id), ['increment_seconds' => 10])
            ->assertStatus(422);

        $this->actingAs($f['pendingUser'])
            ->postJson(route('induction.complete', $f['step2']->id), ['view_time_seconds' => 10])
            ->assertStatus(422);
    }

    public function test_time_engine_validates_required_seconds_before_completing(): void
    {
        $f = $this->makeInductionFixture();
        $step1 = $f['step1'];
        $user = $f['pendingUser'];

        // Intento sin haber acumulado tiempo previo (salto directo): rechazado con 422
        $this->actingAs($user)
            ->postJson(route('induction.complete', $step1->id), ['view_time_seconds' => 30])
            ->assertStatus(422)
            ->assertJson(['completed' => false]);

        // Acumular 15s (insuficiente para 30s)
        $this->actingAs($user)
            ->postJson(route('induction.heartbeat', $step1->id), ['increment_seconds' => 15])
            ->assertOk()
            ->assertJson([
                'view_time_seconds' => 15,
                'remaining_seconds' => 15,
                'can_complete' => false,
            ]);

        $this->actingAs($user)
            ->postJson(route('induction.complete', $step1->id))
            ->assertStatus(422);

        // Acumular otros 15s -> completa los 30s requeridos
        $this->actingAs($user)
            ->postJson(route('induction.heartbeat', $step1->id), ['increment_seconds' => 15])
            ->assertOk()
            ->assertJson([
                'view_time_seconds' => 30,
                'remaining_seconds' => 0,
                'can_complete' => true,
            ]);

        $this->actingAs($user)
            ->postJson(route('induction.complete', $step1->id))
            ->assertOk()
            ->assertJson(['completed' => true]);

        $progress = UserContentProgress::where('user_id', $user->id)
            ->where('content_id', $step1->id)
            ->firstOrFail();

        $this->assertSame('completed', $progress->status);
        $this->assertSame(30, $progress->view_time_seconds);
        $this->assertNotNull($progress->completed_at);

        // Ahora el paso 2 (YouTube iframe) pasa a ser el paso actual
        $this->actingAs($user)
            ->get(route('induction.index'))
            ->assertOk()
            ->assertSee('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0');
    }

    public function test_certify_requires_all_mandatory_contents_completed(): void
    {
        $f = $this->makeInductionFixture();
        $user = $f['pendingUser'];

        $fakePngBase64 = 'data:image/png;base64,' . base64_encode(str_repeat('PNGDATA123456789', 4));

        $this->actingAs($user)
            ->postJson(route('induction.certify'), [
                'accept_terms' => '1',
                'evidence_photo_base64' => $fakePngBase64,
            ])
            ->assertStatus(422)
            ->assertJson(['certified' => false]);

        $this->assertSame('pending', $user->fresh()->induction_status);
        $this->assertSame(0, UserInductionEvidence::where('user_id', $user->id)->count());
    }

    public function test_user_can_certify_with_webcam_base64_and_unlock_dashboard(): void
    {
        $f = $this->makeInductionFixture();
        $user = $f['pendingUser'];

        foreach ([$f['step1'], $f['step2']] as $step) {
            UserContentProgress::create([
                'user_id' => $user->id,
                'content_id' => $step->id,
                'view_time_seconds' => $step->required_time_seconds,
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        // Debe mostrar la pantalla final de certificación, cámara web y firma digital
        $this->actingAs($user)
            ->get(route('induction.index'))
            ->assertOk()
            ->assertSee('Certificación de Cumplimiento y Captura de Evidencia')
            ->assertSee('inductionCertifyForm')
            ->assertSee('signaturePad')
            ->assertSee('signatureBase64');

        $fakeJpegBase64 = 'data:image/jpeg;base64,' . base64_encode(str_repeat("\xFF\xD8\xFF\xE0JFIFDATA", 6));
        $fakeSignaturePng = 'data:image/png;base64,' . base64_encode(str_repeat('PNGSIGNDATA12345', 4));

        $response = $this->actingAs($user)
            ->post(route('induction.certify'), [
                'accept_terms' => '1',
                'evidence_photo_base64' => $fakeJpegBase64,
                'signature_base64' => $fakeSignaturePng,
            ]);

        $response->assertRedirect(route('dashboard.corporate-contents.index'));
        $this->assertSame('completed', $user->fresh()->induction_status);

        $evidence = UserInductionEvidence::where('user_id', $user->id)->firstOrFail();
        $this->assertNotNull($evidence->accepted_at);
        $this->assertNotEmpty($evidence->ip_address);
        $this->assertTrue(Storage::disk('local')->exists($evidence->evidence_photo_path));
        $this->assertNotNull($evidence->signature_path);
        $this->assertTrue(Storage::disk('local')->exists($evidence->signature_path));

        // Con induction_status = completed accede al listado de contenidos y al dashboard
        $this->actingAs($user->fresh())
            ->get(route('dashboard.corporate-contents.index'))
            ->assertOk();

        $this->actingAs($user->fresh())
            ->get(route('dashboard'))
            ->assertOk();

        // Incluso un usuario con rol de backoffice debe ser redirigido al listado de contenidos
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Administrador']);
        $adminUser = User::factory()->create([
            'active' => 1,
            'induction_status' => 'pending',
        ]);
        $adminUser->roles()->attach([$f['role']->id, $adminRole->id]);

        $adminMandatory = CorporateContent::visibleTo($adminUser)
            ->where('corporate_contents.is_mandatory', true)
            ->get();

        foreach ($adminMandatory as $step) {
            UserContentProgress::create([
                'user_id' => $adminUser->id,
                'content_id' => $step->id,
                'view_time_seconds' => $step->required_time_seconds,
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        $this->actingAs($adminUser)
            ->post(route('induction.certify'), [
                'accept_terms' => '1',
                'evidence_photo_base64' => $fakeJpegBase64,
                'signature_base64' => $fakeSignaturePng,
            ])
            ->assertRedirect(route('dashboard.corporate-contents.index'));
    }

    public function test_certify_rejects_file_upload_without_live_selfie_base64(): void
    {
        $f = $this->makeInductionFixture();
        $user = $f['pendingUser'];

        foreach ([$f['step1'], $f['step2']] as $step) {
            UserContentProgress::create([
                'user_id' => $user->id,
                'content_id' => $step->id,
                'view_time_seconds' => $step->required_time_seconds,
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        $this->actingAs($user)
            ->get(route('induction.index'))
            ->assertOk()
            ->assertDontSee('evidence_photo_file')
            ->assertDontSee('evidencePhotoFile')
            ->assertDontSee('generateConstancyBtn');

        $photo = UploadedFile::fake()->createWithContent('selfie.jpg', str_repeat("\xFF\xD8\xFF\xE0JPG", 12));

        $this->actingAs($user)
            ->postJson(route('induction.certify'), [
                'accept_terms' => '1',
                'evidence_photo_file' => $photo,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['evidence_photo_base64']);

        $this->assertSame('pending', $user->fresh()->induction_status);
        $this->assertSame(0, UserInductionEvidence::where('user_id', $user->id)->count());
        $this->assertCount(0, Storage::disk('local')->allFiles('induction/evidences'));
    }

    public function test_certify_requires_digital_signature_without_leaving_orphan_photo(): void
    {
        $f = $this->makeInductionFixture();
        $user = $f['pendingUser'];

        foreach ([$f['step1'], $f['step2']] as $step) {
            UserContentProgress::create([
                'user_id' => $user->id,
                'content_id' => $step->id,
                'view_time_seconds' => $step->required_time_seconds,
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        $fakeJpegBase64 = 'data:image/jpeg;base64,' . base64_encode(str_repeat("\xFF\xD8\xFF\xE0JFIFDATA", 6));

        $this->actingAs($user)
            ->postJson(route('induction.certify'), [
                'accept_terms' => '1',
                'evidence_photo_base64' => $fakeJpegBase64,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['signature_base64']);

        $this->assertSame('pending', $user->fresh()->induction_status);
        $this->assertSame(0, UserInductionEvidence::where('user_id', $user->id)->count());
        $this->assertCount(0, Storage::disk('local')->allFiles('induction/evidences'));
        $this->assertCount(0, Storage::disk('local')->allFiles('induction/signatures'));
    }

    public function test_certify_rejects_malformed_signature_without_leaving_orphan_photo(): void
    {
        $f = $this->makeInductionFixture();
        $user = $f['pendingUser'];

        foreach ([$f['step1'], $f['step2']] as $step) {
            UserContentProgress::create([
                'user_id' => $user->id,
                'content_id' => $step->id,
                'view_time_seconds' => $step->required_time_seconds,
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        $fakeJpegBase64 = 'data:image/jpeg;base64,' . base64_encode(str_repeat("\xFF\xD8\xFF\xE0JFIFDATA", 6));

        $this->actingAs($user)
            ->postJson(route('induction.certify'), [
                'accept_terms' => '1',
                'evidence_photo_base64' => $fakeJpegBase64,
                'signature_base64' => 'not-a-valid-image',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['signature_base64']);

        $this->assertSame('pending', $user->fresh()->induction_status);
        $this->assertSame(0, UserInductionEvidence::where('user_id', $user->id)->count());
        $this->assertCount(0, Storage::disk('local')->allFiles('induction/evidences'));
        $this->assertCount(0, Storage::disk('local')->allFiles('induction/signatures'));
    }

    public function test_middleware_allows_pending_user_when_no_mandatory_contents_exist(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        // Desactivar temporalmente dentro de la transacción contenidos globales obligatorios reales de la BD local
        CorporateContent::whereNull('target_user_id')
            ->whereDoesntHave('roles')
            ->where('is_mandatory', true)
            ->update(['is_active' => false]);

        $suffix = bin2hex(random_bytes(4));
        $role = Role::create(['slug' => 'opt-only-' . $suffix, 'name' => 'Rol Opcional ' . $suffix]);

        $category = DocumentCategory::create(['name' => 'Cat Opcional']);
        $subcategory = DocumentSubcategory::create(['category_id' => $category->id, 'name' => 'Sub Opcional']);

        $optionalContent = CorporateContent::create([
            'subcategory_id' => $subcategory->id,
            'title' => 'Documento No Obligatorio',
            'type' => 'pdf',
            'file_path' => 'induction/pdf/opcional.pdf',
            'required_time_seconds' => 0,
            'target_role_id' => $role->id,
            'is_mandatory' => false,
            'is_active' => true,
        ]);
        $optionalContent->roles()->sync([$role->id]);

        $inactiveMandatory = CorporateContent::create([
            'subcategory_id' => $subcategory->id,
            'title' => 'Obligatorio Inactivo',
            'type' => 'pdf',
            'file_path' => 'induction/pdf/inactivo.pdf',
            'required_time_seconds' => 30,
            'target_role_id' => $role->id,
            'is_mandatory' => true,
            'is_active' => false,
        ]);
        $inactiveMandatory->roles()->sync([$role->id]);

        $user = User::factory()->create([
            'active' => 1,
            'induction_status' => 'pending',
        ]);
        $user->roles()->attach($role->id);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('dashboard.corporate-contents.index'))
            ->assertOk()
            ->assertSee('Documento No Obligatorio');

        $this->actingAs($user)
            ->get(route('induction.index'))
            ->assertRedirect(route('dashboard.corporate-contents.index'));

        $this->assertSame('pending', $user->fresh()->induction_status);
    }

    public function test_middleware_excludes_admin_and_developer_roles_from_mandatory_induction(): void
    {
        $f = $this->makeInductionFixture();

        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Administrador']);
        $devRole = Role::firstOrCreate(['slug' => 'developer'], ['name' => 'Desarrollador']);

        $adminUser = User::factory()->create(['active' => 1, 'induction_status' => 'pending']);
        $adminUser->roles()->attach([$f['role']->id, $adminRole->id]);

        $devUser = User::factory()->create(['active' => 1, 'induction_status' => 'pending']);
        $devUser->roles()->attach([$f['role']->id, $devRole->id]);

        foreach ([$adminUser, $devUser] as $privilegedUser) {
            $this->actingAs($privilegedUser)
                ->get(route('backoffice'))
                ->assertOk();

            $this->actingAs($privilegedUser)
                ->get(route('dashboard'))
                ->assertOk();

            $this->actingAs($privilegedUser)
                ->get(route('dashboard.corporate-contents.index'))
                ->assertOk();
        }
    }
}
