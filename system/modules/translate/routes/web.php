<?php

use Illuminate\Support\Facades\Route;
use Translate\Controllers;

Route::prefix(config('app.management_prefix', 'admin'))
->name('manage.')
->middleware(['admin','auth'])
->group(function() {
    // 批量翻译
    Route::post('translate/batch', [Controllers\TranslateController::class, 'batch'])->name('translate.batch');

    // 翻译一个页面
    Route::post('translate/page', [Controllers\TranslateController::class, 'page'])->name('translate.page');

    // 创建模板并翻译
    Route::post('translate/tpl/{code}', [Controllers\TranslateController::class, 'tpl'])->name('translate.tpl');

    // 获取翻译结果
    Route::post('translate/result', [Controllers\TranslateController::class, 'result'])->name('translate.result');
});