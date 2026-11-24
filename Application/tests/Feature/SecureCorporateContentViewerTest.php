<?php

namespace Tests\Feature;

use App\Models\CorporateContent;
use App\Models\DocumentCategory;
use App\Models\DocumentSubcategory;
use App\Models\Role;
use App\Models\User;
use App\Models\UserContentProgress;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecureCorporateContentViewerTest extends TestCase
{
    use DatabaseTransactions;

    private function pinBrowserSession(): void
    {
        $this->startSession();
        $this->withCredentials();
        $this->withCookie(config('session.cookie'), $this->app['session']->getId());
    }

    private function createContentFixture(
        string $disk = 'local',
        string $type = 'pdf',
        ?int $targetUserId = null,
        bool $hasWatermark = true
    ): array {
        Storage::fake('local');
        Storage::fake('public');

        $role = Role::firstOrCreate(['slug' => 'technician'], ['name' => 'Técnico']);
        $otherRole = Role::firstOrCreate(['slug' => 'supervisor'], ['name' => 'Supervisor']);

        $category = DocumentCategory::create(['name' => 'Categoría Seguridad Test']);
        $subcategory = DocumentSubcategory::create([
            'category_id' => $category->id,
            'name' => 'Subcategoría Test',
        ]);

        $filePath = $type === 'pdf'
            ? 'induction/pdf/test-document.pdf'
            : 'induction/video/test-video.mp4';

        $fileBytes = $type === 'pdf'
            ? "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF"
            : 'fake-mp4-binary-payload';

        Storage::disk($disk)->put($filePath, $fileBytes);

        $content = CorporateContent::create([
            'subcategory_id' => $subcategory->id,
            'title' => 'Documento Confidencial Test',
            'type' => $type,
            'file_path' => $filePath,
            'required_time_seconds' => 30,
            'target_role_id' => $targetUserId ? null : $role->id,
            'target_user_id' => $targetUserId,
            'is_mandatory' => true,
            'has_watermark' => $type === 'pdf' ? $hasWatermark : false,
            'is_active' => true,
        ]);

        if (!$targetUserId) {
            $content->roles()->sync([$role->id]);
        }

        $authorizedUser = User::factory()->create(['active' => 1]);
        $authorizedUser->roles()->attach($role->id);

        $secondAuthorizedUser = User::factory()->create(['active' => 1]);
        $secondAuthorizedUser->roles()->attach($role->id);

        $unauthorizedUser = User::factory()->create(['active' => 1]);
        $unauthorizedUser->roles()->attach($otherRole->id);

        return compact('content', 'role', 'otherRole', 'authorizedUser', 'secondAuthorizedUser', 'unauthorizedUser', 'fileBytes');
    }

    public function test_guest_cannot_request_token_or_stream(): void
    {
        $fixture = $this->createContentFixture();
        $content = $fixture['content'];

        $this->post(route('dashboard.corporate-contents.token', $content->id))
            ->assertRedirect(route('login'));

        $this->get(route('dashboard.corporate-contents.stream', [
            'content' => $content->id,
            'token' => str_repeat('a', 64),
        ]))->assertRedirect(route('login'));
    }

    public function test_authorized_user_receives_html_viewer_instead_of_raw_pdf_on_view_route(): void
    {
        $fixture = $this->createContentFixture();

        // Primera vez sin validación previa redirige al flujo de selfie y firma
        $this->actingAs($fixture['authorizedUser'])
            ->get(route('dashboard.corporate-contents.view', $fixture['content']->id))
            ->assertRedirect(route('dashboard.corporate-contents.verify', $fixture['content']->id));

        UserContentProgress::create([
            'user_id' => $fixture['authorizedUser']->id,
            'content_id' => $fixture['content']->id,
            'view_time_seconds' => 30,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($fixture['authorizedUser'])
            ->get(route('dashboard.corporate-contents.view', $fixture['content']->id));

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'DENY');
        $this->assertStringStartsWith('text/html', (string) $response->headers->get('Content-Type'));
        $response->assertSee('standaloneSecureViewer');
        $response->assertSee('pdf.min.js');
    }

    public function test_library_index_renders_secure_dialog_button_instead_of_direct_link(): void
    {
        $fixture = $this->createContentFixture();

        // Primera vez muestra acceso a verify y no el botón del modal
        $this->actingAs($fixture['authorizedUser'])
            ->get(route('dashboard.corporate-contents.index'))
            ->assertStatus(200)
            ->assertSee(route('dashboard.corporate-contents.verify', $fixture['content']->id), false)
            ->assertSee('Ver y validar documento')
            ->assertSee('secureContentDialog')
            ->assertDontSee('btn btn-outline-primary w-100 py-2 js-open-secure-content', false)
            ->assertDontSee('href="' . route('dashboard.corporate-contents.view', $fixture['content']->id) . '"', false);

        UserContentProgress::create([
            'user_id' => $fixture['authorizedUser']->id,
            'content_id' => $fixture['content']->id,
            'view_time_seconds' => 30,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Luego de la primera validación habilita el botón del modal seguro normal
        $response = $this->actingAs($fixture['authorizedUser'])
            ->get(route('dashboard.corporate-contents.index'));

        $response->assertStatus(200);
        $response->assertSee('btn btn-outline-primary w-100 py-2 js-open-secure-content', false);
        $response->assertSee('Abrir documento');
        $response->assertSee('secureContentDialog');
        $response->assertSee('overscroll-behavior: contain');
        $response->assertDontSee(route('dashboard.corporate-contents.verify', $fixture['content']->id), false);
        $response->assertDontSee('href="' . route('dashboard.corporate-contents.view', $fixture['content']->id) . '"', false);
    }

    public function test_authorized_user_can_issue_token_and_stream_pdf_once_with_hardened_headers(): void
    {
        $fixture = $this->createContentFixture('local', 'pdf');
        $content = $fixture['content'];
        $this->pinBrowserSession();

        $tokenResponse = $this->actingAs($fixture['authorizedUser'])
            ->withHeaders(['Sec-Fetch-Site' => 'same-origin'])
            ->postJson(route('dashboard.corporate-contents.token', $content->id));

        $tokenResponse->assertStatus(200)
            ->assertJsonStructure(['stream_url', 'type', 'title', 'watermark'])
            ->assertJson([
                'type' => 'pdf',
                'title' => 'Documento Confidencial Test',
            ]);

        $streamUrl = $tokenResponse->json('stream_url');

        // Primera petición válida desde el visor
        $streamResponse = $this->actingAs($fixture['authorizedUser'])
            ->withHeaders([
                'Sec-Fetch-Site' => 'same-origin',
                'Sec-Fetch-Mode' => 'cors',
                'Sec-Fetch-Dest' => 'empty',
                'X-Content-Viewer' => '1',
            ])
            ->get($streamUrl);

        $streamResponse->assertStatus(200);
        $streamResponse->assertHeader('Content-Type', 'application/octet-stream');
        $streamResponse->assertHeader('X-Content-Type-Options', 'nosniff');
        $streamResponse->assertHeader('Cross-Origin-Resource-Policy', 'same-origin');
        $streamResponse->assertHeader('X-Frame-Options', 'DENY');
        $this->assertStringContainsString('no-store', (string) $streamResponse->headers->get('Cache-Control'));

        // Segunda petición con el mismo token de PDF debe fallar (un solo uso)
        $this->actingAs($fixture['authorizedUser'])
            ->withHeaders([
                'Sec-Fetch-Site' => 'same-origin',
                'Sec-Fetch-Mode' => 'cors',
                'Sec-Fetch-Dest' => 'empty',
                'X-Content-Viewer' => '1',
            ])
            ->get($streamUrl)
            ->assertStatus(403);
    }

    public function test_stream_rejects_direct_browser_navigation_and_missing_viewer_header(): void
    {
        $fixture = $this->createContentFixture('local', 'pdf');
        $content = $fixture['content'];
        $this->pinBrowserSession();

        $tokenResponse = $this->actingAs($fixture['authorizedUser'])
            ->postJson(route('dashboard.corporate-contents.token', $content->id));

        $streamUrl = $tokenResponse->json('stream_url');

        // Navegación directa en nueva pestaña (barra de direcciones)
        $this->actingAs($fixture['authorizedUser'])
            ->withHeaders([
                'Sec-Fetch-Site' => 'none',
                'Sec-Fetch-Mode' => 'navigate',
                'Sec-Fetch-Dest' => 'document',
            ])
            ->get($streamUrl)
            ->assertStatus(403);

        // Petición same-origin pero sin cabecera X-Content-Viewer
        $this->actingAs($fixture['authorizedUser'])
            ->withHeaders([
                'Sec-Fetch-Site' => 'same-origin',
                'Sec-Fetch-Mode' => 'cors',
                'Sec-Fetch-Dest' => 'empty',
            ])
            ->get($streamUrl)
            ->assertStatus(403);
    }

    public function test_token_cannot_be_used_by_another_authenticated_user_or_different_session(): void
    {
        $fixture = $this->createContentFixture('local', 'pdf');
        $content = $fixture['content'];
        $this->pinBrowserSession();

        $tokenResponse = $this->actingAs($fixture['authorizedUser'])
            ->postJson(route('dashboard.corporate-contents.token', $content->id));

        $streamUrl = $tokenResponse->json('stream_url');

        // Otro usuario autenticado (aunque comparta el rol autorizado)
        $this->actingAs($fixture['secondAuthorizedUser'])
            ->withHeaders([
                'Sec-Fetch-Site' => 'same-origin',
                'Sec-Fetch-Mode' => 'cors',
                'Sec-Fetch-Dest' => 'empty',
                'X-Content-Viewer' => '1',
            ])
            ->get($streamUrl)
            ->assertStatus(403);

        // Mismo usuario pero desde otra sesión distinta
        $this->withCookie(config('session.cookie'), str_repeat('z', 40))
            ->actingAs($fixture['authorizedUser'])
            ->withHeaders([
                'Sec-Fetch-Site' => 'same-origin',
                'Sec-Fetch-Mode' => 'cors',
                'Sec-Fetch-Dest' => 'empty',
                'X-Content-Viewer' => '1',
            ])
            ->get($streamUrl)
            ->assertStatus(403);
    }

    public function test_unauthorized_user_is_blocked_from_view_and_token(): void
    {
        $fixture = $this->createContentFixture('local', 'pdf');

        $this->actingAs($fixture['unauthorizedUser'])
            ->get(route('dashboard.corporate-contents.view', $fixture['content']->id))
            ->assertStatus(403);

        $this->actingAs($fixture['unauthorizedUser'])
            ->postJson(route('dashboard.corporate-contents.token', $fixture['content']->id))
            ->assertStatus(403);
    }

    public function test_legacy_public_disk_files_remain_readable_and_new_uploads_store_in_private_local_disk(): void
    {
        $fixture = $this->createContentFixture('public', 'pdf');
        $legacyContent = $fixture['content'];
        $this->pinBrowserSession();

        $this->assertSame('public', $legacyContent->resolveStorageDisk());

        $tokenResponse = $this->actingAs($fixture['authorizedUser'])
            ->postJson(route('dashboard.corporate-contents.token', $legacyContent->id));

        $tokenResponse->assertStatus(200);

        $this->actingAs($fixture['authorizedUser'])
            ->withHeaders([
                'Sec-Fetch-Site' => 'same-origin',
                'Sec-Fetch-Mode' => 'cors',
                'Sec-Fetch-Dest' => 'empty',
                'X-Content-Viewer' => '1',
            ])
            ->get($tokenResponse->json('stream_url'))
            ->assertStatus(200);

        // Subir nuevo archivo desde el Backoffice debe guardarse en disco 'local' (privado)
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Administrador']);
        $admin = User::factory()->create(['active' => 1]);
        $admin->roles()->attach($adminRole->id);

        $uploadedFile = UploadedFile::fake()->createWithContent(
            'politica-interna.pdf',
            "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF"
        );

        $storeResponse = $this->actingAs($admin)
            ->post(route('induction.contents.store'), [
                'subcategory_id' => $legacyContent->subcategory_id,
                'title' => 'Política Privada',
                'type' => 'pdf',
                'source' => 'file',
                'file' => $uploadedFile,
                'required_time_seconds' => 60,
                'target_type' => 'roles',
                'roles' => [$fixture['role']->id],
                'is_mandatory' => 1,
                'is_active' => 1,
            ]);

        $storeResponse->assertRedirect(route('induction.contents.index'));

        $created = CorporateContent::where('title', 'Política Privada')->firstOrFail();
        $this->assertTrue(Storage::disk('local')->exists($created->file_path));
        $this->assertFalse(Storage::disk('public')->exists($created->file_path));
        $this->assertSame('local', $created->resolveStorageDisk());
    }

    public function test_watermark_is_optional_and_applies_only_to_pdf_documents(): void
    {
        $pdfWithWatermark = $this->createContentFixture('local', 'pdf', null, true);
        $this->pinBrowserSession();

        $tokenWithWm = $this->actingAs($pdfWithWatermark['authorizedUser'])
            ->postJson(route('dashboard.corporate-contents.token', $pdfWithWatermark['content']->id));

        $tokenWithWm->assertStatus(200);
        $this->assertSame(
            'Propiedad exclusiva de Boomer Elecomm, impresa por ' . $pdfWithWatermark['authorizedUser']->name,
            $tokenWithWm->json('watermark')
        );

        $pdfWithoutWatermark = $this->createContentFixture('local', 'pdf', null, false);
        $tokenWithoutWm = $this->actingAs($pdfWithoutWatermark['authorizedUser'])
            ->postJson(route('dashboard.corporate-contents.token', $pdfWithoutWatermark['content']->id));

        $tokenWithoutWm->assertStatus(200);
        $this->assertNull($tokenWithoutWm->json('watermark'));

        $videoFixture = $this->createContentFixture('local', 'video', null, true);
        $tokenVideo = $this->actingAs($videoFixture['authorizedUser'])
            ->postJson(route('dashboard.corporate-contents.token', $videoFixture['content']->id));

        $tokenVideo->assertStatus(200);
        $this->assertNull($tokenVideo->json('watermark'));

        // Backoffice: crear video enviando has_watermark=1 debe guardar false; editar PDF debe alternar correctamente
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Administrador']);
        $admin = User::factory()->create(['active' => 1]);
        $admin->roles()->attach($adminRole->id);

        $this->actingAs($admin)
            ->post(route('induction.contents.store'), [
                'subcategory_id' => $pdfWithWatermark['content']->subcategory_id,
                'title' => 'Video Sin Marca',
                'type' => 'video',
                'source' => 'url',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'required_time_seconds' => 30,
                'target_type' => 'roles',
                'roles' => [$pdfWithWatermark['role']->id],
                'has_watermark' => 1,
                'is_mandatory' => 1,
                'is_active' => 1,
            ])
            ->assertRedirect(route('induction.contents.index'));

        $createdVideo = CorporateContent::where('title', 'Video Sin Marca')->firstOrFail();
        $this->assertFalse($createdVideo->has_watermark);

        // Editar PDF existente para activar y luego desactivar has_watermark
        $pdfContent = $pdfWithoutWatermark['content'];
        $this->actingAs($admin)
            ->put(route('induction.contents.update', $pdfContent->id), [
                'subcategory_id' => $pdfContent->subcategory_id,
                'title' => $pdfContent->title,
                'type' => 'pdf',
                'source' => 'file',
                'required_time_seconds' => 30,
                'target_type' => 'roles',
                'roles' => [$pdfWithoutWatermark['role']->id],
                'has_watermark' => 1,
                'is_mandatory' => 1,
                'is_active' => 1,
            ])
            ->assertRedirect(route('induction.contents.index'));

        $this->assertTrue($pdfContent->fresh()->has_watermark);

        $this->actingAs($admin)
            ->put(route('induction.contents.update', $pdfContent->id), [
                'subcategory_id' => $pdfContent->subcategory_id,
                'title' => $pdfContent->title,
                'type' => 'pdf',
                'source' => 'file',
                'required_time_seconds' => 30,
                'target_type' => 'roles',
                'roles' => [$pdfWithoutWatermark['role']->id],
                'is_mandatory' => 1,
                'is_active' => 1,
            ])
            ->assertRedirect(route('induction.contents.index'));

        $this->assertFalse($pdfContent->fresh()->has_watermark);
    }
}
