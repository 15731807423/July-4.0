<?php

use Illuminate\Support\Facades\Route;
use Translate\Controllers;

Route::prefix(config('app.management_prefix', 'admin'))
->name('manage.')
->middleware(['admin','auth'])
->group(function() {
    // 一键翻译
    Route::post('translate/all', [Controllers\TranslateController::class, 'all'])->name('translate.all');

    // 一键翻译2.0
    Route::get('translate/all2', [Controllers\TranslateController::class, 'all2'])->name('translate.all2');

    // 批量翻译
    Route::post('translate/batch', [Controllers\TranslateController::class, 'batch'])->name('translate.batch');
});