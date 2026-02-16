<?php

use App\Http\Controllers\Admin\AdsManagerController;
use App\Http\Controllers\Admin\CategoryManagerController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ClientObjectiveController;
use App\Http\Controllers\Admin\ConsultingController;
use App\Http\Controllers\Admin\EventTypeController;
use App\Http\Controllers\Admin\ExpertiseManagerController;
use App\Http\Controllers\Admin\FeatureGroupController;
use App\Http\Controllers\Admin\FilterGroupController;
use App\Http\Controllers\Admin\FocusAreaManagerController;
use App\Http\Controllers\Admin\GuestController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ObjectiveManagerController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StatusManagerController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\UserManagerController;
use App\Http\Controllers\Admin\UserPermissionController;
use App\Http\Controllers\Admin\UserTaskController;
use App\Http\Controllers\Admin\VendorServiceController;
use App\Http\Controllers\DashboardController;
use App\Models\VendorService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->get('/admin/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');

    return "✅ All caches cleared!";
});

Route::middleware(['auth', 'user.access'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])
        ->name('dashboard');
    Route::get(
        '/dashboard/day-consultings',
        [DashboardController::class, 'dayConsultings']
    )->name('dashboard.dayConsultings');

    Route::post(
        'dashboard/update-status',
        [DashboardController::class, 'updateStatus']
    )->name('dashboard.update-status');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('admin.profile.destroy');

    //---------------------------------------------------------------
    //----------------- Roles ---------------------------------------

    Route::resource('permissions', PermissionController::class);
    Route::resource('user-manager', UserManagerController::class);
    Route::resource('role', RoleController::class);
    Route::get('roles/{role}/add-permission', [RoleController::class, 'addPermission'])->name('add-role-permisiion');
    Route::put('roles/{role}/give-permission', [RoleController::class, 'givePermission'])->name('give-role-permission');

    // USER PERMISSION MANAGER
    // Route::get('/user-permission', [UserPermissionController::class, 'index'])->name('user.permission.index');

    Route::get('/user-permission/{user}/modal', [UserPermissionController::class, 'loadModal'])
        ->name('user.permission.modal');

    Route::get('user-permission/fetch', [UserPermissionController::class, 'loadRolePermissionForm'])
        ->name('user-permission-fetch');

    Route::put('/user-permission/{user}', [UserPermissionController::class, 'update'])
        ->name('user.permission.update');

    Route::get('/get-states/{country}', [LocationController::class, 'states'])
        ->name('location.states');

    Route::get('/get-cities/{state}', [LocationController::class, 'cities'])
        ->name('location.cities');

    Route::resource('clients', ClientController::class);
    Route::get(
        '/client/consultings',
        [ClientController::class, 'clientConsultings']
    )->name('client.consultings');

    Route::resource('status-manager', StatusManagerController::class);
    Route::resource('objective-manager', ObjectiveManagerController::class);
    Route::resource('expertise-manager', ExpertiseManagerController::class);
    Route::resource('focus-area-manager', FocusAreaManagerController::class);

    Route::resource('lead', LeadController::class);

    Route::prefix('leads')->name('admin.leads.')->group(function () {

        Route::post('update-status', [LeadController::class, 'updateStatus'])
            ->name('update-status');

        Route::post('follow-ups', [LeadController::class, 'followupStore'])
            ->name('followups.store');

        Route::get('{lead}/follow-ups', [LeadController::class, 'followUpsList'])
            ->name('followups.list');
        Route::post('followups/{followUp}/complete', [LeadController::class, 'markCompleted'])->name('followups.status');
    });

    Route::resource('client-objective-manager', ClientObjectiveController::class);
    Route::get('/client-objective/{id}/details', [ClientObjectiveController::class, 'getObjectiveDetails'])->name('client-objective.details');

    Route::get('consulting/sample-download', [ConsultingController::class, 'downloadSample'])
        ->name('consulting.sample.download');
    Route::post('consulting/import', [ConsultingController::class, 'import'])
        ->name('consulting.import');

    Route::resource('consulting', ConsultingController::class);

    Route::resource('task', TaskController::class);
    Route::delete('/task/attachments/{attachment}', [TaskController::class, 'destroyAttachment'])->name('task.attachments.delete');
    Route::get('task/{task}/pdf', [TaskController::class, 'exportTaskPdf'])
        ->name('task.pdf');

    Route::resource('user-task', UserTaskController::class);
    Route::get('user-task/{task}/activities', [UserTaskController::class, 'activities'])
        ->name('user-task.activities');

    Route::prefix('reports')
        ->name('reports.')
        ->group(function () {

            // ================= NEW REPORTS =================

            // Client Report
            Route::get('reports/clients', [ReportController::class, 'clientIndex'])
                ->name('clients');

            // Objective Report
            Route::get('reports/objectives', [ReportController::class, 'objectiveIndex'])
                ->name('objectives');

            // Consulting Report
            Route::get('reports/consultings', [ReportController::class, 'consultingIndex'])
                ->name('consultings');

            // Lead Report
            Route::get('reports/leads', [ReportController::class, 'leadIndex'])
                ->name('leads');

            Route::get('reports/user-tasks', [ReportController::class, 'userTaskReport'])
                ->name('tasks');
        });
});
