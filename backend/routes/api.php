<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Spatie\Permission\Middleware\RoleMiddleware;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobApplicationController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('role:client')->get('/client/dashboard', function () {
        return response()->json(['message' => 'Welcome, Client!']);
    });
    Route::middleware('role:freelancer')->get('/freelancer/dashboard', function () {
        return response()->json(['message' => 'Welcome, Freelancer!']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/freelancers', [ProfileController::class, 'listFreelancers']);
    Route::post('/freelancer/profile', [ProfileController::class, 'store']);
    Route::get('/freelancer/profile', [ProfileController::class, 'show']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/jobs', [JobController::class, 'index']);
    Route::get('/jobs/{id}', [JobController::class, 'show']);
    Route::post('/jobs', [JobController::class, 'store']);
    Route::put('/jobs/{id}', [JobController::class, 'update']);
    Route::delete('/jobs/{id}', [JobController::class, 'destroy']);
    Route::get('/jobs/{jobId}/applied-users', [JobController::class, 'getAppliedUsers']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/jobs/{id}/apply', [JobApplicationController::class, 'apply']);
    Route::get('/freelancer/applied-jobs', [JobApplicationController::class, 'appliedJobs']);
    Route::get('/freelancer/applied-jobs/{id}', [JobApplicationController::class, 'show']);
});
