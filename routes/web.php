<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

// Healthcheck Route
Route::get('/health', [PostController::class, 'healthcheck'])->name('healthcheck');

// Test Index Route
Route::get('/', function () {
    return view('test-index');
})->name('tests.index');

// Dependency Injection Test Routes
Route::get('/dependency-test', [PostController::class, 'index'])->name('dependency.test');
Route::get('/dependency-singleton-test', [PostController::class, 'testSingleton'])->name('dependency.singleton');
Route::get('/shared-dependency-test', [PostController::class, 'testSharedDependency'])->name('dependency.shared');

// Interface-based Dependency Injection Test Routes
Route::get('/interface-dependency-test', [PostController::class, 'testInterfaceDependency'])->name('interface.dependency.test');
Route::get('/interface-singleton-test', [PostController::class, 'testInterfaceSingleton'])->name('interface.singleton');
Route::get('/shared-interface-dependency-test', [PostController::class, 'testSharedInterfaceDependency'])->name('interface.dependency.shared');

// Request Persistence Test Routes
Route::get('/request-persistence-test', [PostController::class, 'testRequestPersistence'])->name('request.persistence.test');
Route::get('/interface-request-persistence-test', [PostController::class, 'testInterfaceRequestPersistence'])->name('interface.request.persistence.test');
Route::get('/clear-persistence-cache', [PostController::class, 'clearPersistenceCache'])->name('persistence.cache.clear');

// Application Injection Test Routes
Route::get('/application-injection-test', [PostController::class, 'testApplicationInjection'])->name('application.injection.test');

// Pure Singleton Pattern Test Routes
Route::get('/pure-singleton-test', [PostController::class, 'testPureSingleton'])->name('pure.singleton.test');

// Request-Scoped Service Test Routes (Octane-Safe)
Route::get('/request-scoped-test', [PostController::class, 'testRequestScoped'])->name('request.scoped.test');