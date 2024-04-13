<?php

use Illuminate\Support\Facades\Route;
use Modules\Permission\App\Http\Controllers\Admin\RoleController;

Route::webSuperGroup('admin', function () {
    Route::resource('roles', RoleController::class)->except(['show']);
}, ['auth:admin', 'role:super_admin']);
