<?php

use App\Modules\Documents\Http\Controllers\AttachmentCategoryController;
use App\Modules\Documents\Http\Controllers\AttachmentController;
use App\Modules\Documents\Http\Controllers\AttachmentDownloadController;
use App\Modules\Documents\Http\Controllers\AttachmentReportController;
use App\Modules\Documents\Http\Controllers\DocumentDashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/documents')
    ->name('documents.')
    ->middleware(['web', 'auth', 'admin.access:documents.view'])
    ->group(function (): void {
        Route::get('/', DocumentDashboardController::class)->name('dashboard');

        Route::get('attachments', [AttachmentController::class, 'index'])->name('attachments.index');
        Route::get('attachments/create', [AttachmentController::class, 'create'])->middleware('admin.access:documents.manage')->name('attachments.create');
        Route::post('attachments', [AttachmentController::class, 'store'])->middleware('admin.access:documents.manage')->name('attachments.store');
        Route::get('attachments/{attachment}', [AttachmentController::class, 'show'])->name('attachments.show');
        Route::get('attachments/{attachment}/edit', [AttachmentController::class, 'edit'])->middleware('admin.access:documents.manage')->name('attachments.edit');
        Route::put('attachments/{attachment}', [AttachmentController::class, 'update'])->middleware('admin.access:documents.manage')->name('attachments.update');
        Route::get('attachments/{attachment}/download', [AttachmentDownloadController::class, 'download'])->name('attachments.download');
        Route::get('attachments/{attachment}/preview', [AttachmentDownloadController::class, 'preview'])->name('attachments.preview');
        Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->middleware('admin.access:documents.delete')->name('attachments.destroy');

        Route::resource('categories', AttachmentCategoryController::class)->except(['show'])->middleware('admin.access:documents.manage');

        Route::prefix('reports')->name('reports.')->group(function (): void {
            Route::get('summary', [AttachmentReportController::class, 'summary'])->name('summary');
            Route::get('by-category', [AttachmentReportController::class, 'byCategory'])->name('by-category');
            Route::get('by-source', [AttachmentReportController::class, 'bySource'])->name('by-source');
        });
    });
