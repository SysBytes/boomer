<?php

use Illuminate\Support\Facades\Route;

// Example Routes
// Route::view('/', 'landing');
// Route::match(['get', 'post'], '/dashboard', function(){
//     return view('dashboard');
// });
// Route::view('/pages/slick', 'pages.slick');
// Route::view('/pages/datatables', 'pages.datatables');
// Route::view('/pages/blank', 'pages.blank');


Route::controller(App\Http\Controllers\HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/servicios', 'index')->name('services');
    Route::get('/nosotros', 'about')->name('about');
    Route::get('/servicios/ingenieria', 'engineering')->name('services.engineering');
    Route::get('/servicios/construccion', 'construction')->name('services.construction');
    Route::get('/servicios/mantenimiento', 'maintenance')->name('services.maintenance');
    Route::get('/servicios/certificaciones', 'certifications')->name('services.certifications');
    Route::get('/galeria', 'gallery')->name('gallery');
});

Auth::routes(['register' => false]);

Route::get('/backoffice', [App\Http\Controllers\BackofficeController::class, 'index'])
    ->name('backoffice')
    ->middleware(['auth', \App\Http\Middleware\CheckBackofficeRole::class]);

Route::get('/dashboard', [App\Http\Controllers\BackofficeController::class, 'dashboard'])->name('dashboard');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth']], function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'editDashboard'])->name('dashboard.profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'updateDashboard'])->name('dashboard.profile.update');

    // Biblioteca de contenidos corporativos del usuario
    Route::get('corporate-contents', [App\Http\Controllers\UserContentLibraryController::class, 'index'])->name('dashboard.corporate-contents.index');
    Route::get('corporate-contents/{content}/view', [App\Http\Controllers\UserContentLibraryController::class, 'viewFile'])->name('dashboard.corporate-contents.view');

    Route::middleware([\App\Http\Middleware\CheckNotAssistant::class])->group(function () {
        Route::get('requisition/request', [App\Http\Controllers\RequisitionController::class, 'createMaterialRequest'])->name('requisitions.request');
        Route::post('requisition/request', [App\Http\Controllers\RequisitionController::class, 'storeMaterialRequest'])->name('requisitions.store_request');
        Route::get('requisition/show/{requisition}', [App\Http\Controllers\RequisitionController::class, 'showDashboard'])->name('requisitions.show');
        Route::post('requisition/show/{requisition}/comment', [App\Http\Controllers\RequisitionController::class, 'addCommentDashboard'])->name('requisitions.comment');
    });

    // Check-In / Check-Out
    Route::get('checkin', [App\Http\Controllers\TimeLogController::class, 'index'])->name('checkin.index');
    Route::post('checkin', [App\Http\Controllers\TimeLogController::class, 'checkIn'])->name('checkin.store');
    Route::post('checkout', [App\Http\Controllers\TimeLogController::class, 'checkOut'])->name('checkin.checkout');

    // Staff Check-In / Check-Out
    Route::get('staff-checkin', [App\Http\Controllers\StaffCheckInController::class, 'index'])->name('staff.checkin.index');
    Route::post('staff-checkin', [App\Http\Controllers\StaffCheckInController::class, 'checkIn'])->name('staff.checkin.store');
    Route::post('staff-checkout', [App\Http\Controllers\StaffCheckInController::class, 'checkOut'])->name('staff.checkin.checkout');

});

Route::group(['prefix' => 'backoffice', 'middleware' => ['auth', \App\Http\Middleware\CheckBackofficeRole::class]], function () {
    Route::get('requisitions', [App\Http\Controllers\RequisitionController::class, 'index'])->name('requisitions.index');
    Route::get('requisitions/{requisition}', [App\Http\Controllers\RequisitionController::class, 'show'])->name('requisitions.status');
    Route::get('requisitions/{requisition}/print', [App\Http\Controllers\RequisitionController::class, 'print'])->name('requisitions.print');

    // Time Logs (Horas)
    Route::post('/timelogs/{timeLog}/remind', [App\Http\Controllers\TimeLogController::class, 'remind'])->name('timelogs.remind');
    Route::get('timelogs', [App\Http\Controllers\TimeLogController::class, 'history'])->name('timelogs.index');
    Route::get('timelogs/manual', [App\Http\Controllers\TimeLogController::class, 'manualCreate'])->name('timelogs.manual');
    Route::post('timelogs/manual', [App\Http\Controllers\TimeLogController::class, 'manualStore'])->name('timelogs.manual.store');
    Route::get('timelogs/{timeLog}', [App\Http\Controllers\TimeLogController::class, 'show'])->name('timelogs.show');
    Route::get('timelogs/{timeLog}/edit', [App\Http\Controllers\TimeLogController::class, 'edit'])->name('timelogs.edit');
    Route::put('timelogs/{timeLog}', [App\Http\Controllers\TimeLogController::class, 'update'])->name('timelogs.update');
    Route::delete('timelogs/{timeLog}', [App\Http\Controllers\TimeLogController::class, 'destroy'])->name('timelogs.destroy');

    // Horas Extras (Overtimes)
    Route::get('overtimes', [App\Http\Controllers\OvertimeController::class, 'index'])->name('overtimes.index');
    Route::post('overtimes/{overtime}/status', [App\Http\Controllers\OvertimeController::class, 'updateStatus'])->name('overtimes.status');
    Route::post('overtimes/recalculate', [App\Http\Controllers\OvertimeController::class, 'recalculate'])->name('overtimes.recalculate');
    Route::delete('overtimes/{overtime}', [App\Http\Controllers\OvertimeController::class, 'destroy'])->name('overtimes.destroy');

    // Purchase Reception
    Route::get('requisitions/{requisition}/purchase', [App\Http\Controllers\RequisitionController::class, 'purchaseReceiveForm'])->name('requisitions.purchase');
    Route::post('requisitions/{requisition}/purchase', [App\Http\Controllers\RequisitionController::class, 'purchaseReceive'])->name('requisitions.purchase_store');

    // Post-Purchase Delivery
    Route::post('requisitions/{requisition}/deliver', [App\Http\Controllers\RequisitionController::class, 'deliver'])->name('requisitions.deliver');
    Route::post('requisitions/{requisition}/priority', [App\Http\Controllers\RequisitionController::class, 'updatePriority'])->name('requisitions.priority');

    Route::post('requisitions/{requisition}/comment', [App\Http\Controllers\RequisitionController::class, 'addCommentBackoffice'])->name('requisitions.comment_backoffice');
    Route::post('requisitions/{requisition}/approve', [App\Http\Controllers\RequisitionController::class, 'approve'])->name('requisitions.approve');
    Route::post('requisitions/{requisition}/reject', [App\Http\Controllers\RequisitionController::class, 'reject'])->name('requisitions.reject');
    Route::post('requisitions/{requisition}/return-pending', [App\Http\Controllers\RequisitionController::class, 'returnToPending'])->name('requisitions.return_pending');
    Route::resource('material_category', App\Http\Controllers\MaterialCategoryController::class);

    // Importar Materiales (Debe ir antes del resource para evitar colisiones con material/{material})
    Route::get('import/material', [App\Http\Controllers\MaterialController::class, 'import'])->name('import.material');
    Route::post('import/material', [App\Http\Controllers\MaterialController::class, 'processImport'])->name('import.material.process');

    Route::resource('material', App\Http\Controllers\MaterialController::class);
    Route::resource('client', App\Http\Controllers\ClientController::class);
    Route::resource('location', App\Http\Controllers\LocationController::class);
    Route::resource('country', App\Http\Controllers\CountryController::class);
    Route::resource('holiday', App\Http\Controllers\HolidayController::class);

    // Configuración General
    Route::get('settings', [App\Http\Controllers\SettingController::class, 'index'])->name('setting.index');
    Route::post('settings', [App\Http\Controllers\SettingController::class, 'update'])->name('setting.update');
    Route::resource('work', App\Http\Controllers\WorkController::class);
    Route::post('work/{work}/duplicate', [App\Http\Controllers\WorkController::class, 'duplicate'])->name('work.duplicate');
    Route::resource('user', App\Http\Controllers\UserController::class);
    Route::resource('role', App\Http\Controllers\RoleController::class);

    // Inducción Corporativa - Gestión de Contenido (admin / developer)
    Route::prefix('induction')->name('induction.')->group(function () {
        Route::resource('categories', App\Http\Controllers\CategoryController::class)
            ->parameters(['categories' => 'category'])
            ->except(['show']);
        Route::resource('subcategories', App\Http\Controllers\SubcategoryController::class)
            ->parameters(['subcategories' => 'subcategory'])
            ->except(['show']);
        Route::post('contents/{content}/toggle', [App\Http\Controllers\CorporateContentController::class, 'toggle'])->name('contents.toggle');
        Route::resource('contents', App\Http\Controllers\CorporateContentController::class)
            ->parameters(['contents' => 'content'])
            ->except(['show']);
    });

    // Herramientas de Desarrollo (Exclusivo rol developer)
    Route::get('developer', [App\Http\Controllers\DeveloperController::class, 'index'])->name('developer.index');
    Route::post('developer/execute', [App\Http\Controllers\DeveloperController::class, 'execute'])->name('developer.execute');
    Route::get('developer/logs', [App\Http\Controllers\DeveloperController::class, 'logs'])->name('developer.logs');
    Route::post('developer/logs/clear', [App\Http\Controllers\DeveloperController::class, 'clearLogs'])->name('developer.logs.clear');
    Route::get('developer/logs/download', [App\Http\Controllers\DeveloperController::class, 'downloadLog'])->name('developer.logs.download');

    // Técnicos por Proyecto
    Route::get('technicians', [App\Http\Controllers\WorkController::class, 'techniciansIndex'])->name('technicians.index');
    Route::get('technicians/{work}/get', [App\Http\Controllers\WorkController::class, 'getTechnicians'])->name('technicians.get');
    Route::post('technicians/{work}/add', [App\Http\Controllers\WorkController::class, 'addTechnician'])->name('technicians.add');
    Route::post('technicians/{work}/remove', [App\Http\Controllers\WorkController::class, 'removeTechnician'])->name('technicians.remove');

    // Vista de prueba Select2
    Route::view('select2', 'backoffice.work.select2')->name('work.select2');

    // Reportes
    Route::get('reports/materials-by-work', [App\Http\Controllers\ReportController::class, 'materialsByWork'])->name('reports.materials_by_work');
    Route::get('reports/inventory', [App\Http\Controllers\ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('reports/work-users', [App\Http\Controllers\ReportController::class, 'workUsers'])->name('reports.work_users');
    Route::get('reports/worked-hours', [App\Http\Controllers\ReportController::class, 'workedHours'])->name('reports.worked_hours');

    // Copias de Seguridad
    Route::get('backups', [App\Http\Controllers\BackupController::class, 'index'])->name('backups.index');
    Route::post('backups/files', [App\Http\Controllers\BackupController::class, 'createFiles'])->name('backups.create_files');
    Route::post('backups/database', [App\Http\Controllers\BackupController::class, 'createDatabase'])->name('backups.create_database');
    Route::get('backups/files/{filename}/contents', [App\Http\Controllers\BackupController::class, 'showContents'])->name('backups.contents');
    Route::post('backups/files/restore', [App\Http\Controllers\BackupController::class, 'restoreFiles'])->name('backups.restore_files');
    Route::post('backups/database/{filename}/restore', [App\Http\Controllers\BackupController::class, 'restoreDatabase'])->name('backups.restore_database');
    Route::get('backups/{type}/{filename}/download', [App\Http\Controllers\BackupController::class, 'download'])->name('backups.download');
    Route::delete('backups/{type}/{filename}', [App\Http\Controllers\BackupController::class, 'delete'])->name('backups.delete');
    Route::get('backups/settings', [App\Http\Controllers\BackupController::class, 'settings'])->name('backups.settings');
    Route::post('backups/settings', [App\Http\Controllers\BackupController::class, 'updateSettings'])->name('backups.update_settings');

    // Ejecutar notificaciones de check-out faltantes manualmente
    Route::get('run-schedule-checkouts', function () {
        \Illuminate\Support\Facades\Artisan::call('app:check-missing-checkouts');
        return nl2br("El comando de notificaciones de check-out ha sido ejecutado manualmente.\n\nResultado:\n" . \Illuminate\Support\Facades\Artisan::output());
    })->name('run.schedule.checkouts');

    // Probar envío de WhatsApp
    Route::get('test-whatsapp', function () {
        $user = auth()->user();
        if (!$user || !$user->phone) {
            return "Error: Tu usuario actual (" . ($user->name ?? 'Invitado') . ") no tiene un número de teléfono guardado en el perfil. Actualiza tu perfil primero.";
        }
        $components = [
            [
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $user->name],
                    ['type' => 'text', 'text' => '12'],
                    ['type' => 'text', 'text' => 'Obra de Prueba'],
                    ['type' => 'text', 'text' => url('/dashboard/checkin')],
                ]
            ]
        ];

        $result = \App\Services\WhatsAppService::sendTemplateMessage($user, 'checkout_reminder', $components);

        if ($result === true) {
            return "¡Éxito! El mensaje de prueba de la plantilla 'checkout_reminder' fue enviado a tu número: {$user->phone}. Revisa tu celular.";
        } else {
            return "Falló el envío de WhatsApp. Error reportado: " . json_encode($result);
        }
    })->name('test.whatsapp');
});
Route::get('/clear', function () {
    Artisan::call('route:clear');
    return "Rutas limpias";
});

Route::any('/phone/{any?}', function () {
    return redirect()->route('home');
})->where('any', '.*');

Route::get('/assign-developer', function () {
    $role = \App\Models\Role::firstOrCreate(['slug' => 'developer'], ['name' => 'Desarrollador']);

    $user = \App\Models\User::where('email', 'websites@sysbytes.com.co')->first();

    if ($user) {
        $user->roles()->syncWithoutDetaching([$role->id]);
        return "Rol de Developer asignado correctamente al usuario websites@sysbytes.com.co";
    }

    return "El usuario websites@sysbytes.com.co no fue encontrado.";
});

Route::get('/create-developer-user', function () {
    $role = \App\Models\Role::firstOrCreate(['slug' => 'developer'], ['name' => 'Desarrollador']);
    $user = \App\Models\User::firstOrCreate(
        ['email' => 'websites@sysbytes.com.co'],
        [
            'name' => 'SysAdmin',
            'password' => bcrypt('password'),
            'country' => 'Global',
            'active' => 1,
        ]
    );
    $user->roles()->syncWithoutDetaching([$role->id]);
    return "Usuario dummy creado o ya existe. Email: admin@boomer-elecomm.com | Password: password";
});

Route::get('/truncate-all', function () {
    if (!auth()->check() || !auth()->user()->hasRole('developer')) {
        abort(403);
    }

    Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    // Truncar en orden de dependencias
    App\Models\TimeLog::truncate();
    Illuminate\Support\Facades\DB::table('material_requisition')->truncate();
    App\Models\Requisition::truncate();
    // App\Models\StockMovement::truncate();
    Illuminate\Support\Facades\DB::table('work_users')->truncate();
    App\Models\Work::truncate();
    App\Models\Location::truncate();
    App\Models\Client::truncate();
    App\Models\Material::truncate();
    App\Models\MaterialCategory::truncate();

    // No truncar usuarios, roles, países, ni feriados para no romper el acceso

    Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    return redirect()->route('dashboard')->with('success', 'Todas las tablas (excepto usuarios y configuración) han sido truncadas correctamente.');
})->name('truncate-all');


Route::get('/ejecutar-migracion-segura', function () {
    // El flag --force es obligatorio en producción
    \Illuminate\Support\Facades\Artisan::call('migrate', [
        '--force' => true,
        '--path' => [
            'database/migrations/2026_06_03_134656_create_countries_table.php',
            'database/migrations/2026_06_03_134657_create_country_user_table.php'
        ]
    ]);
    $output = \Illuminate\Support\Facades\Artisan::output();
    return '<pre>' . $output . '</pre>';
});

Route::get('/limpiar-cache-produccion', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    return 'Cache de rutas y configuración limpiada correctamente.';
});
