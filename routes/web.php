<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', function () {
    return view('login');
})->name('login.view');
Route::get('/register-view', function () {
    return view('register');
})->name('register.view');

Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('register');

Route::get('/assign-admin/{userId}', function ($userId) {
    $user = \App\Models\User::findOrFail($userId);

    $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

    $user->assignRole($adminRole);

    return response()->json([
        'message' => "User {$user->name} đã được cấp quyền admin",
        'roles' => $user->getRoleNames()
    ]);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('dashboard');}
    )->name('dashboard');

    Route::middleware(['checkRole:admin'])->prefix('admin')->group(function () {
        // Role
        Route::get('roles', [\App\Http\Controllers\Admin\RolePermissionController::class, 'listRoles']);
        Route::post('roles', [\App\Http\Controllers\Admin\RolePermissionController::class, 'createRole']);
        Route::put('roles/{id}', [\App\Http\Controllers\Admin\RolePermissionController::class, 'updateRole']);
        Route::delete('roles/{id}', [\App\Http\Controllers\Admin\RolePermissionController::class, 'deleteRole']);

        // Permission
        Route::get('permissions', [\App\Http\Controllers\Admin\RolePermissionController::class, 'listPermissions']);
        Route::post('permissions', [\App\Http\Controllers\Admin\RolePermissionController::class, 'createPermission']);
        Route::put('permissions/{id}', [\App\Http\Controllers\Admin\RolePermissionController::class, 'updatePermission']);
        Route::delete('permissions/{id}', [\App\Http\Controllers\Admin\RolePermissionController::class, 'deletePermission']);

        // Assign
        Route::post('users/{id}/assign-role', [\App\Http\Controllers\Admin\RolePermissionController::class, 'assignRoleToUser']);
        Route::post('roles/{id}/assign-permission', [\App\Http\Controllers\Admin\RolePermissionController::class, 'assignPermissionToRole']);

        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::put('/users/{id}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::delete('/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/{id}/assign-role', [\App\Http\Controllers\Admin\UserController::class, 'assignRole'])->name('users.assignRole');

        Route::get('/upload-settings', [\App\Http\Controllers\Admin\UploadSettingController::class, 'getSettings'])->name('upload.settings.get');
        Route::post('/upload-settings', [\App\Http\Controllers\Admin\UploadSettingController::class, 'updateSettings'])->name('upload.settings.update');
    });
    Route::middleware(['checkRole:admin|document'])->prefix('admin')->group(function () {
        Route::post('/documents', [\App\Http\Controllers\DocumentController::class, 'store'])->name('documents.store');
        Route::resource('documents', \App\Http\Controllers\DocumentController::class);
        Route::get('/documents/{id}/edit', [\App\Http\Controllers\DocumentController::class, 'edit'])->name('documents.edit');
        Route::put('/documents/{id}', [\App\Http\Controllers\DocumentController::class, 'update'])->name('documents.update');
    });
    Route::get('/documents', [\App\Http\Controllers\DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/file/{filename}', [\App\Http\Controllers\DocumentController::class, 'viewFile'])->name('documents.viewFile');
});
