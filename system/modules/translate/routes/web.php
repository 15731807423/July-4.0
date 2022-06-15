<?php

use Illuminate\Support\Facades\Route;
use Translate\Controllers;

Route::prefix(config('app.management_prefix', 'admin'))
->name('manage.')
->middleware(['admin','auth'])
->group(function() {
    // 一键翻译
    Route::post('translate/all', [Controllers\TranslateController::class, 'all'])->name('translate.all');

    // 批量翻译
    Route::post('translate/batch', [Controllers\TranslateController::class, 'batch'])->name('translate.batch');

    // 创建模板并翻译
    Route::post('translate/tpl/{code}', [Controllers\TranslateController::class, 'tpl'])->name('translate.tpl');

    // 获取翻译结果
    Route::post('translate/result', [Controllers\TranslateController::class, 'result'])->name('translate.result');
});