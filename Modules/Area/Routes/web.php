<?php

use Modules\Area\Http\Controllers\Admin\CityController;
use Modules\Area\Http\Controllers\Admin\ProvinceController;

Route::webSuperGroup('admin', function () {
    Route::resource('provinces', ProvinceController::class)->only(['index']);
    Route::resource('cities', CityController::class);
});
