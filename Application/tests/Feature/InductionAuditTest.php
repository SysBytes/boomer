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

class InductionAuditTest extends TestCase
{
    use DatabaseTransactions;

    private function createAuditFixture(): array
    {
        Storage::fake('local');
        Storage::fake('public');

        $suffix = bin2hex(random_bytes(4));
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Administrador']);
        $devRole = Role::firstOrCreate(['slug' => 'developer'], ['name' => 'Desarrollador']);
        $supervisorRole = Role::firstOrCreate(['slug' => 'supervisor'], ['name' => 'Supervisor']);
        $techRole = Role::create(['slug' => 'audit-tech-' . $suffix, 'name' => 'Técnico Audit ' . $suffix]);

        $category = DocumentCategory::create(['name' => 'Cat Auditoría ' . $suffix]);
        $subcategory = DocumentSubcategory::create(['category_id' => $category->id, 'name' => 'Sub Auditoría ' . $suffix]);

        $content = CorporateContent::create([
            'subcategory_id' => $subcategory->id,
            'title' => 'Manual Auditoría ' . $suffix,
            'type' => 'pdf',
            'file_path' => 'induction/pdf/manual-audit.pdf',
            'required_time_seconds' => 45,
            'is_mandatory' => true,
            'is_active' => true,
        ]);
        $content->roles()->sync([$techRole->id]);

        $admin = User::factory()->create(['active' => 1, 'induction_status' => 'completed']);
        $admin->roles()->attach($adminRole->id);

        $developer = User::factory()->create(['active' => 1, 'name' => 'Dev Oculto ' . $suffix, 'induction_status' => 'completed']);
        $developer->roles()->attach($devRole->id);

        $supervisor = User::factory()->create(['active' => 1, 'induction_status' => 'completed']);
        $supervisor->roles()->attach($supervisorRole->id);

        $completedEmp = User::factory()->create([
            'active' => 1,
            'name' => 'Laura Certificada ' . $suffix,
            'email' => 'laura.' . $suffix . '@boomer.test',
            'induction_status' => 'completed',
        ]);
        $completedEmp->roles()->attach($techRole->id);

        $pendingEmp = User::factory()->create([
            'active' => 1,
            'name' => 'Mario Pendiente ' . $suffix,
            'email' => 'mario.' . $suffix . '@boomer.test',
            'induction_status' => 'pending',
        ]);
        $pendingEmp->roles()->attach($techRole->id);

        Storage::disk('local')->put('induction/evidences/laura_' . $suffix . '.jpg', str_repeat('JPGDATA', 10));
        Storage::disk('local')->put('induction/signatures/laura_' . $suffix . '.png', str_repeat('PNGDATA', 10));

        $evidence = UserInductionEvidence::create([
            'user_id' => $completedEmp->id,
            'content_id' => $content->id,
            'evidence_photo_path' => 'induction/evidences/laura_' . $suffix . '.jpg',
            'signature_path' => 'induction/signatures/laura_' . $suffix . '.png',
            'ip_address' => '190.10.20.30',
            'accepted_at' => now()->setDate(2026, 11, 20)->setTime(9, 15, 0),
        ]);

        UserContentProgress::create([
            'user_id' => $completedEmp->id,
            'content_id' => $content->id,
            'view_time_seconds' => 65,
            'status' => 'completed',
            'completed_at' => now()->setDate(2026, 11, 20)->setTime(9, 14, 0),
        ]);

        Storage::disk('local')->put('induction/evidences/dev_' . $suffix . '.jpg', str_repeat('JPGDATA', 10));
        $devEvidence = UserInductionEvidence::create([
            'user_id' => $developer->id,
            'content_id' => null,
            'evidence_photo_path' => 'induction/evidences/dev_' . $suffix . '.jpg',
            'signature_path' => null,
            'ip_address' => '127.0.0.1',
            'accepted_at' => now(),
        ]);

        return compact(
            'suffix',
            'content',
            'admin',
            'developer',
            'supervisor',
            'completedEmp',
            'pendingEmp',
            'evidence',
            'devEvidence'
        );
    }

    public function test_only_admin_and_developer_can_access_audit_panel_and_private_evidence_files(): void
    {
        $f = $this->createAuditFixture();

        $indexUrl = route('induction.audit.index');
        $showUrl = route('induction.audit.show', $f['completedEmp']->id);
        $photoUrl = route('induction.audit.evidence_file', [$f['evidence']->id, 'photo']);
        $signatureUrl = route('induction.audit.evidence_file', [$f['evidence']->id, 'signature']);

        // Invitado redirige a login
        $this->get($indexUrl)->assertRedirect(route('login'));
        $this->get($photoUrl)->assertRedirect(route('login'));

        // Usuario estándar y supervisor reciben 403
        foreach ([$f['completedEmp'], $f['supervisor']] as $unauthorized) {
            $this->actingAs($unauthorized)->get($indexUrl)->assertStatus(403);
            $this->actingAs($unauthorized)->getJson($showUrl)->assertStatus(403);
            $this->actingAs($unauthorized)->get($photoUrl)->assertStatus(403);
        }

        // Admin accede al panel, al detalle y a los archivos privados con cabeceras seguras
        $this->actingAs($f['admin'])
            ->get($indexUrl)
            ->assertOk()
            ->assertSee('Auditoría Gerencial de Inducción')
            ->assertSee('Auditoría de Inducción')
            ->assertDontSee('Solo lectura · Registros inmutables')
            ->assertDontSee($f['pendingEmp']->email)
            ->assertDontSee($f['developer']->email);

        foreach ([$photoUrl, $signatureUrl] as $fileUrl) {
            $res = $this->actingAs($f['admin'])->get($fileUrl);
            $res->assertOk();
            $res->assertHeader('X-Frame-Options', 'DENY');
            $this->assertStringContainsString('no-store', (string) $res->headers->get('Cache-Control'));
        }

        // Aislamiento developer: admin no puede consultar detalle ni archivo de un developer
        $this->actingAs($f['admin'])
            ->getJson(route('induction.audit.show', $f['developer']->id))
            ->assertStatus(403);

        $this->actingAs($f['admin'])
            ->get(route('induction.audit.evidence_file', [$f['devEvidence']->id, 'photo']))
            ->assertStatus(403);

        // Developer sí puede ver su propia evidencia
        $this->actingAs($f['developer'])
            ->get(route('induction.audit.evidence_file', [$f['devEvidence']->id, 'photo']))
            ->assertOk();
    }

    public function test_audit_filters_by_search_status_and_date_range(): void
    {
        $f = $this->createAuditFixture();

        // Usuario pendiente SIN contenidos validados no debe aparecer en auditoría
        $this->actingAs($f['admin'])
            ->get(route('induction.audit.index', ['search' => $f['suffix']]))
            ->assertOk()
            ->assertSee($f['completedEmp']->email)
            ->assertDontSee($f['pendingEmp']->email);

        // Usuario con evidencia registrada (ej. validó un contenido) pero induction_status = pending sí aparece al filtrar por pending
        $validatedPendingEmp = User::factory()->create([
            'active' => 1,
            'name' => 'Sofia Validada ' . $f['suffix'],
            'email' => 'sofia.' . $f['suffix'] . '@boomer.test',
            'induction_status' => 'pending',
        ]);
        Storage::disk('local')->put('induction/evidences/sofia_' . $f['suffix'] . '.jpg', str_repeat('JPGDATA', 10));
        UserInductionEvidence::create([
            'user_id' => $validatedPendingEmp->id,
            'content_id' => $f['content']->id,
            'evidence_photo_path' => 'induction/evidences/sofia_' . $f['suffix'] . '.jpg',
            'signature_path' => null,
            'ip_address' => '190.10.20.31',
            'accepted_at' => now()->setDate(2026, 11, 21)->setTime(11, 0, 0),
        ]);
        UserContentProgress::create([
            'user_id' => $validatedPendingEmp->id,
            'content_id' => $f['content']->id,
            'view_time_seconds' => 45,
            'status' => 'completed',
            'completed_at' => now()->setDate(2026, 11, 21)->setTime(11, 0, 0),
        ]);

        // Búsqueda por nombre/correo
        $this->actingAs($f['admin'])
            ->get(route('induction.audit.index', ['search' => $f['completedEmp']->email]))
            ->assertOk()
            ->assertSee($f['completedEmp']->email)
            ->assertDontSee($validatedPendingEmp->email);

        // Filtro por estado: pending (solo muestra usuarios que ya validaron algún contenido)
        $this->actingAs($f['admin'])
            ->get(route('induction.audit.index', ['search' => $f['suffix'], 'status' => 'pending']))
            ->assertOk()
            ->assertSee($validatedPendingEmp->email)
            ->assertDontSee($f['pendingEmp']->email)
            ->assertDontSee($f['completedEmp']->email);

        // Filtro por estado: completed + rango de fechas coincidente (2026-11-20)
        $this->actingAs($f['admin'])
            ->get(route('induction.audit.index', [
                'search' => $f['suffix'],
                'status' => 'completed',
                'date_from' => '2026-11-19',
                'date_to' => '2026-11-20',
            ]))
            ->assertOk()
            ->assertSee($f['completedEmp']->email)
            ->assertSee('190.10.20.30')
            ->assertDontSee($validatedPendingEmp->email);

        // Rango de fechas fuera de la certificación excluye al empleado
        $this->actingAs($f['admin'])
            ->get(route('induction.audit.index', [
                'search' => $f['suffix'],
                'date_from' => '2026-12-01',
                'date_to' => '2026-12-10',
            ]))
            ->assertOk()
            ->assertDontSee($f['completedEmp']->email);
    }

    public function test_audit_show_returns_evidences_and_progress_and_routes_are_strictly_immutable(): void
    {
        $f = $this->createAuditFixture();
        $showUrl = route('induction.audit.show', $f['completedEmp']->id);

        $this->actingAs($f['admin'])
            ->getJson($showUrl)
            ->assertOk()
            ->assertJsonPath('employee.email', $f['completedEmp']->email)
            ->assertJsonPath('employee.induction_status', 'completed')
            ->assertJsonPath('evidences.0.ip_address', '190.10.20.30')
            ->assertJsonPath('evidences.0.accepted_at_label', '20/11/2026 09:15:00')
            ->assertJsonPath('evidences.0.photo_url', route('induction.audit.evidence_file', [$f['evidence']->id, 'photo']))
            ->assertJsonPath('evidences.0.signature_url', route('induction.audit.evidence_file', [$f['evidence']->id, 'signature']))
            ->assertJsonPath('progress.0.title', $f['content']->title)
            ->assertJsonPath('progress.0.view_time_seconds', 65)
            ->assertJsonPath('progress.0.view_time_label', '01:05 (65s)');

        // Inmutabilidad: no existen rutas de modificación ni eliminación en auditoría
        $this->actingAs($f['admin'])->delete($showUrl)->assertStatus(405);
        $this->actingAs($f['admin'])->put($showUrl)->assertStatus(405);
        $this->actingAs($f['admin'])->post($showUrl)->assertStatus(405);
    }
}
