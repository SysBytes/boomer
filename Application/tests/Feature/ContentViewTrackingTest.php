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
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContentViewTrackingTest extends TestCase
{
    use DatabaseTransactions;

    private function fixture(): array
    {
        Storage::fake('local');
        Storage::fake('public');

        // Roles exclusivos del test para aislar los conteos de los datos reales de la BD local
        $suffix = bin2hex(random_bytes(4));
        $role = Role::create(['slug' => 'tracking-role-' . $suffix, 'name' => 'Tracking ' . $suffix]);
        $otherRole = Role::create(['slug' => 'tracking-other-' . $suffix, 'name' => 'Other ' . $suffix]);
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Administrador']);

        $category = DocumentCategory::create(['name' => 'Cat Tracking']);
        $subcategory = DocumentSubcategory::create(['category_id' => $category->id, 'name' => 'Sub Tracking']);

        $content = CorporateContent::create([
            'subcategory_id' => $subcategory->id,
            'title' => 'Contenido Tracking',
            'type' => 'video',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'required_time_seconds' => 0,
            'is_mandatory' => true,
            'is_active' => true,
        ]);
        $content->roles()->sync([$role->id]);

        $viewer = User::factory()->create(['active' => 1, 'name' => 'ana viewer']);
        $viewer->roles()->attach($role->id);
        $pendingUser = User::factory()->create(['active' => 1, 'name' => 'pedro pendiente']);
        $pendingUser->roles()->attach($role->id);
        $inactiveUser = User::factory()->create(['active' => 0]);
        $inactiveUser->roles()->attach($role->id);
        $outsider = User::factory()->create(['active' => 1]);
        $outsider->roles()->attach($otherRole->id);
        $admin = User::factory()->create(['active' => 1]);
        $admin->roles()->attach($adminRole->id);

        return compact('content', 'viewer', 'pendingUser', 'inactiveUser', 'outsider', 'admin', 'role', 'subcategory');
    }

    private function verifyContentForUser(User $user, CorporateContent $content, bool $withSignature = true): \Illuminate\Testing\TestResponse
    {
        $payload = [
            'accept_terms' => '1',
            'evidence_photo_base64' => 'data:image/jpeg;base64,' . base64_encode(str_repeat("\xFF\xD8\xFF\xE0JFIFDATA", 6)),
        ];

        if ($withSignature) {
            $payload['signature_base64'] = 'data:image/png;base64,' . base64_encode(str_repeat('PNGSIGNDATA12345', 4));
        }

        return $this->actingAs($user)
            ->post(route('dashboard.corporate-contents.verify.store', $content->id), $payload);
    }

    public function test_first_view_requires_selfie_and_signature_verification_and_subsequent_opens_keep_first_timestamp(): void
    {
        $f = $this->fixture();
        $viewedUrl = route('dashboard.corporate-contents.viewed', $f['content']->id);
        $verifyUrl = route('dashboard.corporate-contents.verify', $f['content']->id);

        // Antes de validar por primera vez, el endpoint viewed no permite registrar vista sin selfie/firma
        $this->actingAs($f['viewer'])
            ->postJson($viewedUrl)
            ->assertStatus(422)
            ->assertJson([
                'viewed' => false,
                'requires_verification' => true,
            ]);

        $this->assertSame(
            0,
            UserContentProgress::where('user_id', $f['viewer']->id)
                ->where('content_id', $f['content']->id)
                ->where('status', 'completed')
                ->count()
        );

        // La pantalla de primera visualización renderiza el formulario de selfie y firma
        $this->actingAs($f['viewer'])
            ->get($verifyUrl)
            ->assertOk()
            ->assertSee('inductionCertifyForm')
            ->assertSee('signaturePad');

        $this->travelTo(now()->setDate(2026, 11, 23)->setTime(10, 15, 0));

        $verifyRes = $this->verifyContentForUser($f['viewer'], $f['content'], true);
        $verifyRes->assertRedirect(route('dashboard.corporate-contents.index'))
            ->assertSessionHas('success');

        $evidence = UserInductionEvidence::where('user_id', $f['viewer']->id)
            ->where('content_id', $f['content']->id)
            ->firstOrFail();

        $this->assertTrue(Storage::disk('local')->exists($evidence->evidence_photo_path));
        $this->assertNotNull($evidence->signature_path);
        $this->assertTrue(Storage::disk('local')->exists($evidence->signature_path));

        $row = UserContentProgress::where('user_id', $f['viewer']->id)
            ->where('content_id', $f['content']->id)
            ->firstOrFail();
        $this->assertSame('completed', $row->status);
        $this->assertSame('23/11/2026 10:15', $row->completed_at->format('d/m/Y H:i'));

        // Una segunda visita a verify redirige a la biblioteca (no pide selfie de nuevo)
        $this->actingAs($f['viewer'])
            ->get($verifyUrl)
            ->assertRedirect(route('dashboard.corporate-contents.index'));

        // Aperturas posteriores en el modal conservan la primera fecha/hora
        $this->travelTo(now()->addDay());
        $second = $this->actingAs($f['viewer'])->postJson($viewedUrl);
        $second->assertOk()->assertJson([
            'viewed' => true,
            'first_time' => false,
            'completed_at_label' => '23/11/2026 10:15',
        ]);

        $this->travelBack();
    }

    public function test_verify_enforces_required_time_seconds_when_configured(): void
    {
        $f = $this->fixture();

        $timedContent = CorporateContent::create([
            'subcategory_id' => $f['subcategory']->id,
            'title' => 'Contenido Opcional Con Tiempo',
            'type' => 'video',
            'url' => 'https://vimeo.com/123456789',
            'required_time_seconds' => 20,
            'is_mandatory' => false,
            'is_active' => true,
        ]);
        $timedContent->roles()->sync([$f['role']->id]);

        $fakeSelfie = 'data:image/jpeg;base64,' . base64_encode(str_repeat("\xFF\xD8\xFF\xE0JFIFDATA", 6));
        $fakeSignature = 'data:image/png;base64,' . base64_encode(str_repeat('PNGSIGNDATA12345', 4));

        $this->actingAs($f['viewer'])
            ->postJson(route('dashboard.corporate-contents.verify.store', $timedContent->id), [
                'accept_terms' => '1',
                'evidence_photo_base64' => $fakeSelfie,
                'signature_base64' => $fakeSignature,
            ])
            ->assertStatus(422)
            ->assertJson(['verified' => false]);

        $this->actingAs($f['viewer'])
            ->postJson(route('dashboard.corporate-contents.heartbeat', $timedContent->id), [
                'increment_seconds' => 20,
            ])
            ->assertOk()
            ->assertJson(['can_complete' => true]);

        $this->actingAs($f['viewer'])
            ->postJson(route('dashboard.corporate-contents.verify.store', $timedContent->id), [
                'accept_terms' => '1',
                'evidence_photo_base64' => $fakeSelfie,
                'signature_base64' => $fakeSignature,
            ])
            ->assertOk()
            ->assertJson(['verified' => true]);

        $this->assertSame(
            1,
            UserInductionEvidence::where('user_id', $f['viewer']->id)
                ->where('content_id', $timedContent->id)
                ->count()
        );
    }

    public function test_non_recipient_cannot_register_view_and_admin_preview_is_not_recorded(): void
    {
        $f = $this->fixture();
        $viewedUrl = route('dashboard.corporate-contents.viewed', $f['content']->id);
        $verifyUrl = route('dashboard.corporate-contents.verify', $f['content']->id);

        $this->actingAs($f['outsider'])->get($verifyUrl)->assertStatus(403);
        $this->actingAs($f['outsider'])->postJson($viewedUrl)->assertStatus(403);
        $this->verifyContentForUser($f['outsider'], $f['content'])->assertStatus(403);

        $this->actingAs($f['admin'])->postJson($viewedUrl)
            ->assertOk()
            ->assertJson(['viewed' => false, 'preview' => true]);

        $this->assertSame(0, UserContentProgress::where('content_id', $f['content']->id)->count());
        $this->assertSame(0, UserInductionEvidence::where('content_id', $f['content']->id)->count());
    }

    public function test_library_shows_viewed_date_and_pending_state(): void
    {
        $f = $this->fixture();

        $this->actingAs($f['pendingUser'])
            ->get(route('dashboard.corporate-contents.index'))
            ->assertOk()
            ->assertSee('Pendiente por ver')
            ->assertSee(route('dashboard.corporate-contents.verify', $f['content']->id), false);

        $this->travelTo(now()->setDate(2026, 11, 23)->setTime(8, 5, 0));
        $this->verifyContentForUser($f['viewer'], $f['content'], true)
            ->assertRedirect(route('dashboard.corporate-contents.index'));

        $this->actingAs($f['viewer'])
            ->get(route('dashboard.corporate-contents.index'))
            ->assertOk()
            ->assertSee('Visto el 23/11/2026 08:05')
            ->assertSee('js-open-external-content')
            ->assertDontSee('Pendiente por ver')
            ->assertDontSee(route('dashboard.corporate-contents.verify', $f['content']->id), false);
        $this->travelBack();
    }

    public function test_backoffice_viewers_lists_viewed_and_pending_only_among_active_recipients(): void
    {
        $f = $this->fixture();

        $this->verifyContentForUser($f['viewer'], $f['content'])->assertRedirect(route('dashboard.corporate-contents.index'));

        $response = $this->actingAs($f['admin'])
            ->get(route('induction.contents.viewers', $f['content']->id));

        $response->assertOk();
        $response->assertSeeInOrder(['Visualizados', 'Pendientes']);
        $response->assertSee('Ana Viewer');
        $response->assertSee('Pedro Pendiente');
        $response->assertDontSee($f['inactiveUser']->email);
        $response->assertDontSee($f['outsider']->email);

        $this->assertSame(1, $response->viewData('viewed')->count());
        $this->assertSame(1, $response->viewData('pending')->count());
        $this->assertSame($f['viewer']->id, $response->viewData('viewed')->first()->user->id);
        $this->assertSame($f['pendingUser']->id, $response->viewData('pending')->first()->id);
    }

    public function test_backoffice_index_shows_counts_and_viewers_route_is_restricted(): void
    {
        $f = $this->fixture();
        $this->verifyContentForUser($f['viewer'], $f['content'])->assertRedirect(route('dashboard.corporate-contents.index'));

        $response = $this->actingAs($f['admin'])
            ->get(route('induction.contents.index'))
            ->assertOk()
            ->assertSee('vistos')
            ->assertSee('pendientes');

        $row = $response->viewData('contents')->firstWhere('id', $f['content']->id);
        $this->assertSame(2, $row->recipients_count);
        $this->assertSame(1, $row->viewed_count);

        $this->actingAs($f['viewer'])
            ->get(route('induction.contents.viewers', $f['content']->id))
            ->assertStatus(403);
    }

    public function test_backoffice_can_reset_user_view_and_reopen_mandatory_induction(): void
    {
        $f = $this->fixture();
        $f['viewer']->update(['induction_status' => 'completed']);

        $this->verifyContentForUser($f['viewer'], $f['content'])->assertRedirect(route('dashboard.corporate-contents.index'));

        $evidence = UserInductionEvidence::where('content_id', $f['content']->id)
            ->where('user_id', $f['viewer']->id)
            ->firstOrFail();

        $photoPath = $evidence->evidence_photo_path;
        $signaturePath = $evidence->signature_path;
        $this->assertTrue(Storage::disk('local')->exists($photoPath));
        $this->assertTrue(Storage::disk('local')->exists($signaturePath));

        $this->assertSame(
            1,
            UserContentProgress::where('content_id', $f['content']->id)
                ->where('user_id', $f['viewer']->id)
                ->count()
        );

        // Non-admin user cannot reset views
        $this->actingAs($f['viewer'])
            ->delete(route('induction.contents.viewers.reset', [$f['content']->id, $f['viewer']->id]))
            ->assertStatus(403);

        // Non-developer admin cannot reset a developer user's view
        $devRole = Role::firstOrCreate(['slug' => 'developer'], ['name' => 'Desarrollador']);
        $devUser = User::factory()->create(['active' => 1, 'induction_status' => 'completed']);
        $devUser->roles()->attach($devRole->id);

        $this->actingAs($f['admin'])
            ->delete(route('induction.contents.viewers.reset', [$f['content']->id, $devUser->id]))
            ->assertStatus(403);

        // Admin resets the mandatory content view for viewer -> progress, evidence rows & files deleted, induction reopened
        $this->actingAs($f['admin'])
            ->delete(route('induction.contents.viewers.reset', [$f['content']->id, $f['viewer']->id]))
            ->assertRedirect(route('induction.contents.viewers', $f['content']->id))
            ->assertSessionHas('success');

        $this->assertSame(
            0,
            UserContentProgress::where('content_id', $f['content']->id)
                ->where('user_id', $f['viewer']->id)
                ->count()
        );
        $this->assertSame(
            0,
            UserInductionEvidence::where('content_id', $f['content']->id)
                ->where('user_id', $f['viewer']->id)
                ->count()
        );
        $this->assertFalse(Storage::disk('local')->exists($photoPath));
        $this->assertFalse(Storage::disk('local')->exists($signaturePath));

        $freshViewer = $f['viewer']->fresh();
        $this->assertSame('pending', $freshViewer->induction_status);

        $viewersResponse = $this->actingAs($f['admin'])
            ->get(route('induction.contents.viewers', $f['content']->id))
            ->assertOk();

        $this->assertSame(0, $viewersResponse->viewData('viewed')->count());
        $this->assertSame(2, $viewersResponse->viewData('pending')->count());

        // User is now forced back to mandatory induction when visiting dashboard
        $this->actingAs($freshViewer)
            ->get(route('dashboard'))
            ->assertRedirect(route('induction.index'));

        // Optional content reset clears progress and puts verify link back without changing induction_status from completed
        $optionalRole = Role::create(['slug' => 'opt-reset-' . bin2hex(random_bytes(3)), 'name' => 'Opt Reset']);
        $optionalContent = CorporateContent::create([
            'subcategory_id' => $f['content']->subcategory_id,
            'title' => 'Contenido Opcional Reset',
            'type' => 'video',
            'url' => 'https://vimeo.com/123456789',
            'required_time_seconds' => 0,
            'is_mandatory' => false,
            'is_active' => true,
        ]);
        $optionalContent->roles()->sync([$optionalRole->id]);

        $completedUser = User::factory()->create(['active' => 1, 'induction_status' => 'completed']);
        $completedUser->roles()->attach($optionalRole->id);

        $this->verifyContentForUser($completedUser, $optionalContent)->assertRedirect(route('dashboard.corporate-contents.index'));

        $this->actingAs($f['admin'])
            ->delete(route('induction.contents.viewers.reset', [$optionalContent->id, $completedUser->id]))
            ->assertRedirect(route('induction.contents.viewers', $optionalContent->id))
            ->assertSessionHas('success');

        $this->assertSame(
            0,
            UserContentProgress::where('content_id', $optionalContent->id)
                ->where('user_id', $completedUser->id)
                ->count()
        );
        $this->assertSame('completed', $completedUser->fresh()->induction_status);

        // El usuario vuelve a ver la obligación de selfie+firma la próxima vez en la biblioteca
        $this->actingAs($completedUser->fresh())
            ->get(route('dashboard.corporate-contents.index'))
            ->assertOk()
            ->assertSee(route('dashboard.corporate-contents.verify', $optionalContent->id), false);
    }

    public function test_backoffice_viewers_shows_reading_confirmation_detail_and_serves_private_evidence_files(): void
    {
        $f = $this->fixture();

        $this->travelTo(now()->setDate(2026, 11, 23)->setTime(14, 30, 45));
        $this->verifyContentForUser($f['viewer'], $f['content'], true)
            ->assertRedirect(route('dashboard.corporate-contents.index'));

        $evidence = UserInductionEvidence::where('user_id', $f['viewer']->id)
            ->where('content_id', $f['content']->id)
            ->firstOrFail();

        $photoRoute = route('induction.evidences.file', [$evidence->id, 'photo']);
        $signatureRoute = route('induction.evidences.file', [$evidence->id, 'signature']);

        $response = $this->actingAs($f['admin'])
            ->get(route('induction.contents.viewers', $f['content']->id))
            ->assertOk()
            ->assertSee('js-open-evidence-modal')
            ->assertSee('evidenceDetailDialog')
            ->assertSee('23/11/2026 14:30:45')
            ->assertSee($photoRoute, false)
            ->assertSee($signatureRoute, false);

        $viewedRow = $response->viewData('viewed')->firstWhere('user.id', $f['viewer']->id);
        $this->assertNotNull($viewedRow->evidence);
        $this->assertSame($evidence->id, $viewedRow->evidence->id);

        // Admin puede visualizar selfie y firma con cabeceras privadas sin caché
        foreach ([$photoRoute, $signatureRoute] as $fileUrl) {
            $fileRes = $this->actingAs($f['admin'])->get($fileUrl);
            $fileRes->assertOk();
            $fileRes->assertHeader('X-Frame-Options', 'DENY');
            $this->assertStringContainsString('no-store', (string) $fileRes->headers->get('Cache-Control'));

            // Usuario estándar no puede consultar evidencias del backoffice
            $this->actingAs($f['viewer'])->get($fileUrl)->assertStatus(403);
        }

        // Aislamiento de rol developer: un admin sin rol developer no puede abrir evidencia de un developer
        $devRole = Role::firstOrCreate(['slug' => 'developer'], ['name' => 'Desarrollador']);
        $devUser = User::factory()->create(['active' => 1]);
        $devUser->roles()->attach($devRole->id);

        Storage::disk('local')->put('induction/evidences/dev_selfie.jpg', str_repeat('JPG', 20));
        Storage::disk('local')->put('induction/signatures/dev_sign.png', str_repeat('PNG', 20));

        $devEvidence = UserInductionEvidence::create([
            'user_id' => $devUser->id,
            'content_id' => $f['content']->id,
            'evidence_photo_path' => 'induction/evidences/dev_selfie.jpg',
            'signature_path' => 'induction/signatures/dev_sign.png',
            'ip_address' => '127.0.0.1',
            'accepted_at' => now(),
        ]);

        $this->actingAs($f['admin'])
            ->get(route('induction.evidences.file', [$devEvidence->id, 'photo']))
            ->assertStatus(403);

        $this->actingAs($devUser)
            ->get(route('induction.evidences.file', [$devEvidence->id, 'photo']))
            ->assertOk();

        // Respaldo para contenido obligatorio certificado desde /induction (content_id null)
        $legacyUser = User::factory()->create(['active' => 1, 'name' => 'carlos legado']);
        $legacyUser->roles()->attach($f['content']->roles->first()->id);

        UserContentProgress::create([
            'user_id' => $legacyUser->id,
            'content_id' => $f['content']->id,
            'view_time_seconds' => 0,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        Storage::disk('local')->put('induction/evidences/general_selfie.jpg', str_repeat('JPG', 20));
        Storage::disk('local')->put('induction/signatures/general_sign.png', str_repeat('PNG', 20));

        $generalEvidence = UserInductionEvidence::create([
            'user_id' => $legacyUser->id,
            'content_id' => null,
            'evidence_photo_path' => 'induction/evidences/general_selfie.jpg',
            'signature_path' => 'induction/signatures/general_sign.png',
            'ip_address' => '10.0.0.5',
            'accepted_at' => now(),
        ]);

        $fallbackViewersRes = $this->actingAs($f['admin'])
            ->get(route('induction.contents.viewers', $f['content']->id))
            ->assertOk()
            ->assertSee(route('induction.evidences.file', [$generalEvidence->id, 'photo']), false);

        $legacyRow = $fallbackViewersRes->viewData('viewed')->firstWhere('user.id', $legacyUser->id);
        $this->assertNotNull($legacyRow->evidence);
        $this->assertSame($generalEvidence->id, $legacyRow->evidence->id);

        $this->travelBack();
    }
}
