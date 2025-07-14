<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BasicDependencyController;
use App\Http\Controllers\SingletonDependencyController;
use App\Http\Controllers\SharedDependencyController;
use App\Http\Controllers\InterfaceDependencyController;
use App\Http\Controllers\InterfaceSingletonController;
use App\Http\Controllers\SharedInterfaceDependencyController;
use App\Http\Controllers\RequestPersistenceController;
use App\Http\Controllers\InterfaceRequestPersistenceController;
use App\Http\Controllers\ApplicationInjectionController;
use App\Http\Controllers\PureSingletonController;
use App\Http\Controllers\RequestScopedController;
use App\Http\Controllers\HealthcheckController;
use App\Http\Controllers\CacheManagementController;

// Healthcheck Route
Route::get('/health', [HealthcheckController::class, 'healthcheck'])->name('healthcheck');

// Test Index Route
Route::get('/', function () {
    return view('test-index');
})->name('tests.index');

// Basic Dependency Injection Test Routes
Route::get('/dependency-test', [BasicDependencyController::class, 'index'])->name('dependency.test');

// Singleton Dependency Injection Test Routes
Route::get('/dependency-singleton-test', [SingletonDependencyController::class, 'testSingleton'])->name('dependency.singleton');

// Shared Dependency Injection Test Routes
Route::get('/shared-dependency-test', [SharedDependencyController::class, 'testSharedDependency'])->name('dependency.shared');

// Interface-based Dependency Injection Test Routes
Route::get('/interface-dependency-test', [InterfaceDependencyController::class, 'testInterfaceDependency'])->name('interface.dependency.test');

// Interface Singleton Test Routes
Route::get('/interface-singleton-test', [InterfaceSingletonController::class, 'testInterfaceSingleton'])->name('interface.singleton');

// Shared Interface Dependency Test Routes
Route::get('/shared-interface-dependency-test', [SharedInterfaceDependencyController::class, 'testSharedInterfaceDependency'])->name('interface.dependency.shared');

// Request Persistence Test Routes
Route::get('/request-persistence-test', [RequestPersistenceController::class, 'testRequestPersistence'])->name('request.persistence.test');

// Interface Request Persistence Test Routes
Route::get('/interface-request-persistence-test', [InterfaceRequestPersistenceController::class, 'testInterfaceRequestPersistence'])->name('interface.request.persistence.test');

// Cache Management Routes
Route::get('/clear-persistence-cache', [CacheManagementController::class, 'clearPersistenceCache'])->name('persistence.cache.clear');

// Application Injection Test Routes
Route::get('/application-injection-test', [ApplicationInjectionController::class, 'testApplicationInjection'])->name('application.injection.test');

// Pure Singleton Pattern Test Routes
Route::get('/pure-singleton-test', [PureSingletonController::class, 'testPureSingleton'])->name('pure.singleton.test');

// Request-Scoped Service Test Routes (Octane-Safe)
Route::get('/request-scoped-test', [RequestScopedController::class, 'testRequestScoped'])->name('request.scoped.test');