<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Services\JobService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class JobController extends Controller
{
    protected $jobService;

    public function __construct(JobService $jobService)
    {
        $this->middleware('auth');
        $this->jobService = $jobService;
    }

    public function createJob(StoreJobRequest $request)
    {
        try {
            $job = $this->jobService->createJob($request->validated());
            return response()->json(['message' => 'Job posted successfully', 'job' => $job], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create job', 'message' => $e->getMessage()], 500);
        }
    }

    public function listJobs()
    {
        try {
            $jobs = $this->jobService->listJobs();
            return response()->json($jobs);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch jobs', 'message' => $e->getMessage()], 500);
        }
    }

    public function getApplicantsWithBids($encodedJobId)
    {
        try {
            $jobId = base64_decode($encodedJobId);
            $applicants = $this->jobService->getApplicantsWithBids($jobId);
            return response()->json($applicants);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Job not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch applicants', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateJob(UpdateJobRequest $request, $id)
    {
        try {
            $job = $this->jobService->updateJob($id, $request->validated());
            return response()->json(['message' => 'Job updated successfully', 'job' => $job]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Job not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update job', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteJob($id)
    {
        try {
            $this->jobService->deleteJob($id);
            return response()->json(['message' => 'Job deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Job not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete job', 'message' => $e->getMessage()], 500);
        }
    }
}
