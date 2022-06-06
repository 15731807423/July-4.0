<?php

use Illuminate\Support\Facades\Route;
use Installer\Controllers\UpdateController;
use Installer\Controllers\InstallController;

// 安装
Route::get('install', [InstallController::class, 'home'])->name('install.home');
Route::post('install', [InstallController::class, 'install'])->name('install.install');
Route::post('install/migrate', [InstallController::class, 'migrate'])->name('install.migrate');

// 切换数据库
Route::get('update/db', [UpdateController::class, 'dbHome'])->name('update.db');
Route::post('update/db', [UpdateController::class, 'dbUpdate'])->name('update.db.update');