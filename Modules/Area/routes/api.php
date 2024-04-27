<?php

use Modules\Area\Http\Controllers\Api\V1\ProvinceController;
use Modules\Area\Http\Controllers\Api\V1\CityController;

Route::name('api')->prefix('admin')->group(function() {
    Route::get('provinces', [ProvinceController::class, 'index'])->name('provinces.index');
    Route::get('cities/{province}', [CityController::class, 'index'])->name('cities.index');
});
