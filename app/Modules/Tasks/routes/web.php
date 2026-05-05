<?php

use App\Modules\Tasks\Http\Controllers\TaskAssignmentController;
use App\Modules\Tasks\Http\Controllers\TaskChecklistController;
use App\Modules\Tasks\Http\Controllers\TaskController;
use App\Modules\Tasks\Http\Controllers\TaskDashboardController;
use App\Modules\Tasks\Http\Controllers\TaskUpdateController;
use App\Modules\Tasks\Http\Controllers\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/tasks')
    ->name('tasks.')
    ->middleware(['web', 'auth', 'admin.access:tasks.view'])
    ->group(function (): void {
        Route::get('/', TaskDashboardController::class)->name('dashboard');

        Route::get('work-orders', [WorkOrderController::class, 'index'])->middleware('admin.access:work-orders.view')->name('work-orders.index');
        Route::get('work-orders/create', [WorkOrderController::class, 'create'])->middleware('admin.access:work-orders.create')->name('work-orders.create');
        Route::post('work-orders', [WorkOrderController::class, 'store'])->middleware('admin.access:work-orders.create')->name('work-orders.store');
        Route::get('work-orders/{workOrder}', [WorkOrderController::class, 'show'])->middleware('admin.access:work-orders.view')->name('work-orders.show');
        Route::get('work-orders/{workOrder}/edit', [WorkOrderController::class, 'edit'])->middleware('admin.access:work-orders.update')->name('work-orders.edit');
        Route::put('work-orders/{workOrder}', [WorkOrderController::class, 'update'])->middleware('admin.access:work-orders.update')->name('work-orders.update');
        Route::post('work-orders/{workOrder}/cancel', [WorkOrderController::class, 'cancel'])->middleware('admin.access:work-orders.cancel')->name('work-orders.cancel');

        Route::get('items', [TaskController::class, 'index'])->name('items.index');
        Route::get('items/create', [TaskController::class, 'create'])->middleware('admin.access:tasks.create')->name('items.create');
        Route::post('items', [TaskController::class, 'store'])->middleware('admin.access:tasks.create')->name('items.store');
        Route::get('items/{task}', [TaskController::class, 'show'])->name('items.show');
        Route::get('items/{task}/edit', [TaskController::class, 'edit'])->middleware('admin.access:tasks.update')->name('items.edit');
        Route::put('items/{task}', [TaskController::class, 'update'])->middleware('admin.access:tasks.update')->name('items.update');
        Route::post('items/{task}/assign', [TaskAssignmentController::class, 'store'])->middleware('admin.access:tasks.assign')->name('items.assign');
        Route::post('items/{task}/start', [TaskController::class, 'start'])->middleware('admin.access:tasks.update')->name('items.start');
        Route::post('items/{task}/submit', [TaskController::class, 'submit'])->middleware('admin.access:tasks.submit')->name('items.submit');
        Route::post('items/{task}/approve', [TaskController::class, 'approve'])->middleware('admin.access:tasks.approve')->name('items.approve');
        Route::post('items/{task}/reject', [TaskController::class, 'reject'])->middleware('admin.access:tasks.approve')->name('items.reject');
        Route::post('items/{task}/cancel', [TaskController::class, 'cancel'])->middleware('admin.access:tasks.cancel')->name('items.cancel');
        Route::post('items/{task}/updates', [TaskUpdateController::class, 'store'])->middleware('admin.access:tasks.submit')->name('items.updates.store');
        Route::post('items/{task}/checklist', [TaskChecklistController::class, 'store'])->middleware('admin.access:tasks.update')->name('items.checklist.store');
        Route::patch('checklist/{item}/toggle', [TaskChecklistController::class, 'toggle'])->middleware('admin.access:tasks.submit')->name('checklist.toggle');
    });
