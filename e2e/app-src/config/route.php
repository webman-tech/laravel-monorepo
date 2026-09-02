<?php

use Webman\Route;
use app\controller\StorageController;
use app\controller\TranslatorController;
use app\controller\ValidatorController;

Route::get('/e2e/storage', [StorageController::class, 'index']);
Route::get('/e2e/storage/extend', [StorageController::class, 'extend']);
Route::get('/e2e/translator', [TranslatorController::class, 'index']);
Route::get('/e2e/validator', [ValidatorController::class, 'index']);
Route::get('/e2e/health', function () {
    return json(['status' => 'ok']);
});
