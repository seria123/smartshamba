<?php

use App\Modules\Livestock\Http\Controllers\LivestockAnimalController;
use App\Modules\Livestock\Http\Controllers\LivestockAnimalGroupController;
use App\Modules\Livestock\Http\Controllers\LivestockBreedController;
use App\Modules\Livestock\Http\Controllers\LivestockDashboardController;
use App\Modules\Livestock\Http\Controllers\LivestockRecordController;
use App\Modules\Livestock\Http\Controllers\LivestockSpeciesController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/livestock')
    ->name('livestock.')
    ->middleware(['web', 'auth', 'admin.access:livestock.view'])
    ->group(function (): void {
        Route::get('/', LivestockDashboardController::class)->name('dashboard');

        Route::get('species', [LivestockSpeciesController::class, 'index'])->middleware('admin.access:livestock-species.view')->name('species.index');
        Route::get('species/create', [LivestockSpeciesController::class, 'create'])->middleware('admin.access:livestock-species.create')->name('species.create');
        Route::post('species', [LivestockSpeciesController::class, 'store'])->middleware('admin.access:livestock-species.create')->name('species.store');
        Route::get('species/{species}', [LivestockSpeciesController::class, 'show'])->middleware('admin.access:livestock-species.view')->name('species.show');
        Route::get('species/{species}/edit', [LivestockSpeciesController::class, 'edit'])->middleware('admin.access:livestock-species.update')->name('species.edit');
        Route::put('species/{species}', [LivestockSpeciesController::class, 'update'])->middleware('admin.access:livestock-species.update')->name('species.update');
        Route::post('species/{species}/deactivate', [LivestockSpeciesController::class, 'deactivate'])->middleware('admin.access:livestock-species.deactivate')->name('species.deactivate');

        Route::get('breeds', [LivestockBreedController::class, 'index'])->middleware('admin.access:livestock-species.view')->name('breeds.index');
        Route::get('breeds/create', [LivestockBreedController::class, 'create'])->middleware('admin.access:livestock-species.create')->name('breeds.create');
        Route::post('breeds', [LivestockBreedController::class, 'store'])->middleware('admin.access:livestock-species.create')->name('breeds.store');
        Route::get('breeds/{breed}', [LivestockBreedController::class, 'show'])->middleware('admin.access:livestock-species.view')->name('breeds.show');
        Route::get('breeds/{breed}/edit', [LivestockBreedController::class, 'edit'])->middleware('admin.access:livestock-species.update')->name('breeds.edit');
        Route::put('breeds/{breed}', [LivestockBreedController::class, 'update'])->middleware('admin.access:livestock-species.update')->name('breeds.update');
        Route::post('breeds/{breed}/deactivate', [LivestockBreedController::class, 'deactivate'])->middleware('admin.access:livestock-species.deactivate')->name('breeds.deactivate');

        Route::get('animals', [LivestockAnimalController::class, 'index'])->middleware('admin.access:livestock-animals.view')->name('animals.index');
        Route::get('animals/create', [LivestockAnimalController::class, 'create'])->middleware('admin.access:livestock-animals.create')->name('animals.create');
        Route::post('animals', [LivestockAnimalController::class, 'store'])->middleware('admin.access:livestock-animals.create')->name('animals.store');
        Route::get('animals/{animal}', [LivestockAnimalController::class, 'show'])->middleware('admin.access:livestock-animals.view')->name('animals.show');
        Route::get('animals/{animal}/edit', [LivestockAnimalController::class, 'edit'])->middleware('admin.access:livestock-animals.update')->name('animals.edit');
        Route::put('animals/{animal}', [LivestockAnimalController::class, 'update'])->middleware('admin.access:livestock-animals.update')->name('animals.update');
        Route::post('animals/{animal}/deactivate', [LivestockAnimalController::class, 'deactivate'])->middleware('admin.access:livestock-animals.deactivate')->name('animals.deactivate');

        Route::get('groups', [LivestockAnimalGroupController::class, 'index'])->middleware('admin.access:livestock-groups.view')->name('groups.index');
        Route::get('groups/create', [LivestockAnimalGroupController::class, 'create'])->middleware('admin.access:livestock-groups.create')->name('groups.create');
        Route::post('groups', [LivestockAnimalGroupController::class, 'store'])->middleware('admin.access:livestock-groups.create')->name('groups.store');
        Route::get('groups/{group}', [LivestockAnimalGroupController::class, 'show'])->middleware('admin.access:livestock-groups.view')->name('groups.show');
        Route::get('groups/{group}/edit', [LivestockAnimalGroupController::class, 'edit'])->middleware('admin.access:livestock-groups.update')->name('groups.edit');
        Route::put('groups/{group}', [LivestockAnimalGroupController::class, 'update'])->middleware('admin.access:livestock-groups.update')->name('groups.update');
        Route::post('groups/{group}/deactivate', [LivestockAnimalGroupController::class, 'deactivate'])->middleware('admin.access:livestock-groups.deactivate')->name('groups.deactivate');

        Route::post('animals/{animal}/treatments', [LivestockRecordController::class, 'storeAnimalTreatment'])->middleware('admin.access:livestock-health.create')->name('animals.treatments.store');
        Route::post('groups/{group}/treatments', [LivestockRecordController::class, 'storeGroupTreatment'])->middleware('admin.access:livestock-health.create')->name('groups.treatments.store');
        Route::post('animals/{animal}/breeding', [LivestockRecordController::class, 'storeAnimalBreeding'])->middleware('admin.access:livestock-breeding.create')->name('animals.breeding.store');
        Route::post('groups/{group}/breeding', [LivestockRecordController::class, 'storeGroupBreeding'])->middleware('admin.access:livestock-breeding.create')->name('groups.breeding.store');
        Route::post('animals/{animal}/pregnancy-checks', [LivestockRecordController::class, 'storeAnimalPregnancyCheck'])->middleware('admin.access:livestock-breeding.create')->name('animals.pregnancy-checks.store');
        Route::post('groups/{group}/pregnancy-checks', [LivestockRecordController::class, 'storeGroupPregnancyCheck'])->middleware('admin.access:livestock-breeding.create')->name('groups.pregnancy-checks.store');
        Route::post('animals/{animal}/births', [LivestockRecordController::class, 'storeAnimalBirth'])->middleware('admin.access:livestock-births.create')->name('animals.births.store');
        Route::post('groups/{group}/births', [LivestockRecordController::class, 'storeGroupBirth'])->middleware('admin.access:livestock-births.create')->name('groups.births.store');
        Route::post('animals/{animal}/weights', [LivestockRecordController::class, 'storeAnimalWeight'])->middleware('admin.access:livestock-feed.create')->name('animals.weights.store');
        Route::post('groups/{group}/weights', [LivestockRecordController::class, 'storeGroupWeight'])->middleware('admin.access:livestock-feed.create')->name('groups.weights.store');
        Route::post('animals/{animal}/feed', [LivestockRecordController::class, 'storeAnimalFeed'])->middleware('admin.access:livestock-feed.create')->name('animals.feed.store');
        Route::post('groups/{group}/feed', [LivestockRecordController::class, 'storeGroupFeed'])->middleware('admin.access:livestock-feed.create')->name('groups.feed.store');
        Route::post('animals/{animal}/movements', [LivestockRecordController::class, 'storeAnimalMovement'])->middleware('admin.access:livestock-movements.create')->name('animals.movements.store');
        Route::post('groups/{group}/movements', [LivestockRecordController::class, 'storeGroupMovement'])->middleware('admin.access:livestock-movements.create')->name('groups.movements.store');
        Route::post('animals/{animal}/mortality', [LivestockRecordController::class, 'storeAnimalMortality'])->middleware('admin.access:livestock-mortality.create')->name('animals.mortality.store');
        Route::post('groups/{group}/mortality', [LivestockRecordController::class, 'storeGroupMortality'])->middleware('admin.access:livestock-mortality.create')->name('groups.mortality.store');
        Route::post('animals/{animal}/yields', [LivestockRecordController::class, 'storeAnimalYield'])->middleware('admin.access:livestock-yields.create')->name('animals.yields.store');
        Route::post('groups/{group}/yields', [LivestockRecordController::class, 'storeGroupYield'])->middleware('admin.access:livestock-yields.create')->name('groups.yields.store');
    });
