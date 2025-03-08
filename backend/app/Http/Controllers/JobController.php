<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Services\JobService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Helpers\EncryptionHelper;
use Illuminate\Support\Facades\Log;



class JobController extends Controller
{
    protected $jobService;

    public function __construct(JobService $jobService)
    {
        $this->middleware('auth');
        $this->jobService = $jobService;
    }


    public function createJob(Request $request)
    {
        try {
            // Decrypt the incoming data
            $decryptedData = json_decode(EncryptionHelper::decodeId($request->input('data')), true);
            // Validate the decrypted data using Laravel's Validator
            $validated = validator($decryptedData, [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'budget' => 'required|numeric|min:0',
                'category' => 'required|string'
            ])->validate();

            $job = $this->jobService->createJob($validated);
            return response()->json(['message' => 'Job posted successfully', 'job' => $job], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
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
            $jobId = EncryptionHelper::decodeId($encodedJobId);
            $applicants = $this->jobService->getApplicantsWithBids($jobId);
            return response()->json($applicants);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Job not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch applicants', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateJob(Request $request, $id)
    {
        try {
            // Decrypt the incoming request data
            $decryptedData = json_decode(EncryptionHelper::decodeId($request->input('encrypted')), true);

            // Validate decrypted data
            $validator = Validator::make($decryptedData, (new UpdateJobRequest())->rules());

            if ($validator->fails()) {
                return response()->json(['error' => 'Validation failed', 'messages' => $validator->errors()], 422);
            }

            // Update the job with decrypted & validated data
            $job = $this->jobService->updateJob($id, $validator->validated());

            return response()->json(['message' => 'Job updated successfully', 'job' => $job]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Job not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update job', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteJob($encodedJobId)
    {
        try {
            $jobId = EncryptionHelper::decodeId($encodedJobId);
            $this->jobService->deleteJob($jobId);
            return response()->json(['message' => 'Job deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Job not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete job', 'message' => $e->getMessage()], 500);
        }
    }
}
