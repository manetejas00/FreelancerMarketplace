<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobApplicationController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'registerUser']);
    Route::post('/login', [AuthController::class, 'authenticateUser']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logoutUser']);

    Route::middleware('role:client')->get('/client/dashboard', fn () => response()->json(['message' => 'Welcome, Client!']));
    Route::middleware('role:freelancer')->get('/freelancer/dashboard', fn () => response()->json(['message' => 'Welcome, Freelancer!']));

    Route::prefix('freelancer')->group(function () {
        Route::get('/profile', [ProfileController::class, 'showFreelancerProfile']);
        Route::post('/profile', [ProfileController::class, 'saveFreelancerProfile']);
        Route::get('/applied-jobs', [JobApplicationController::class, 'listAppliedJobs']);
        Route::get('/applied-jobs/{id}', [JobApplicationController::class, 'getApplicationDetails']);
    });

    Route::prefix('jobs')->group(function () {
        Route::get('/', [JobController::class, 'listJobs']);
        Route::post('/', [JobController::class, 'createJob']);
        Route::put('/{id}', [JobController::class, 'updateJob']);
        Route::delete('/{id}', [JobController::class, 'deleteJob']);
        Route::get('/{jobId}/applicants', [JobController::class, 'getApplicantsWithBids']);
        Route::post('/{id}/apply', [JobApplicationController::class, 'submitApplication']);

    });
    Route::post('/bids/{userId}/{jobId}/update-status', [JobApplicationController::class, 'updateBidStatus']);
    Route::get('/users/{id}/applied-users-details', [ProfileController::class, 'getUserDetailsApplicants']);
    Route::get('/freelancers', [ProfileController::class, 'listFreelancers']);
});
