<?php

use Illuminate\Support\Facades\Route;
use Translate\Controllers\TaskController as Task;
use Translate\Controllers\DirectController as Direct;

Route::prefix(config('app.management_prefix', 'admin'))
->name('manage.')
->middleware(['admin','auth'])
->group(function() {
    // 直接翻译获取结果
    // 批量翻译
    Route::post('translate/direct/batch', [Direct::class, 'batch'])->name('translate.direct.batch');

    // 翻译一个页面
    Route::post('translate/direct/page', [Direct::class, 'page'])->name('translate.direct.page');

    // 创建模板并翻译
    Route::post('translate/direct/tpl', [Direct::class, 'tpl'])->name('translate.direct.tpl');



    // 创建任务获取结果
    // 批量翻译
    Route::post('translate/task/batch', [Task::class, 'batch'])->name('translate.task.batch');

    // 翻译一个页面
    Route::post('translate/task/page', [Task::class, 'page'])->name('translate.task.page');

    // 创建模板并翻译
    Route::post('translate/task/tpl', [Task::class, 'tpl'])->name('translate.task.tpl');

    // 批量翻译 获取结果
    Route::post('translate/task/batch/result', [Task::class, 'batchResult'])->name('translate.task.batch.result');

    // 翻译一个页面 获取结果
    Route::post('translate/task/page/result', [Task::class, 'pageResult'])->name('translate.task.page.result');

    // 创建模板并翻译 获取结果
    Route::post('translate/task/tpl/result', [Task::class, 'tplResult'])->name('translate.task.tpl.result');
});