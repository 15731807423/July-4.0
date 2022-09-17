<?php

use Illuminate\Support\Facades\Route;
use Google\Controllers;

Route::prefix(config('app.management_prefix', 'admin'))
->name('manage.')
->middleware(['admin','auth'])
->group(function() {
    // Route::get('google/index', [Controllers\IndexController::class, 'index'])->name('google.index');
    // 登录页
    Route::get('google/login', [Controllers\UserController::class, 'login'])->name('google.login');

    // 退出页
    Route::get('google/logout', [Controllers\UserController::class, 'logout'])->name('google.logout');


    // Google Search Console page
    Route::get('google/searchConsole/searchAnalytics', [Controllers\SearchConsoleController::class, 'searchAnalytics'])->name('google.searchConsole.searchAnalytics');
    Route::get('google/searchConsole/siteMap', [Controllers\SearchConsoleController::class, 'siteMap'])->name('google.searchConsole.siteMap');
    Route::get('google/searchConsole/urlInspection', [Controllers\SearchConsoleController::class, 'urlInspection'])->name('google.searchConsole.urlInspection');

    // Google Search Console api
    Route::post('google/searchConsole/searchAnalyticsApi', [Controllers\SearchConsoleController::class, 'searchAnalyticsApi'])->name('google.searchConsole.searchAnalyticsApi');
    Route::post('google/searchConsole/siteMapDeleteApi', [Controllers\SearchConsoleController::class, 'siteMapDeleteApi'])->name('google.searchConsole.siteMapDeleteApi');
    Route::post('google/searchConsole/siteMapListApi', [Controllers\SearchConsoleController::class, 'siteMapListApi'])->name('google.searchConsole.siteMapListApi');
    Route::post('google/searchConsole/siteMapSubmitApi', [Controllers\SearchConsoleController::class, 'siteMapSubmitApi'])->name('google.searchConsole.siteMapSubmitApi');
    Route::post('google/searchConsole/urlInspectionApi', [Controllers\SearchConsoleController::class, 'urlInspectionApi'])->name('google.searchConsole.urlInspectionApi');

    // Google Analytics page
    Route::get('google/analytics/a', [Controllers\AnalyticsController::class, 'a'])->name('google.analytics.a');
    Route::get('google/ads/a', [Controllers\AdsController::class, 'a'])->name('google.ads.a');
});