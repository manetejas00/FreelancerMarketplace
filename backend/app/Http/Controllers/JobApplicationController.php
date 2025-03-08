<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitApplicationRequest;
use App\Services\JobApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Events\NewNotification;
use App\Helpers\EncryptionHelper;
use Illuminate\Support\Facades\Validator;


class JobApplicationController extends Controller
{
    protected $jobApplicationService;

    public function __construct(JobApplicationService $jobApplicationService)
    {
        $this->jobApplicationService = $jobApplicationService;
    }

    public function submitApplication(Request $request, $encodedJobId)
    {
        try {
            // 🔓 Decrypt job ID
            $jobId = EncryptionHelper::decodeId($encodedJobId);
            if (!$jobId) {
                return response()->json(['error' => 'Invalid job ID'], 400);
            }

            // 🔓 Decrypt request data
            $encryptedData = $request->input('encrypted');
            logger($request);
            if (!$encryptedData) {
                return response()->json(['error' => 'Missing encrypted data'], 400);
            }

            $decryptedJson = EncryptionHelper::decodeId($encryptedData);
            $decryptedData = json_decode($decryptedJson, true);

            // Ensure decryption was successful
            if (!is_array($decryptedData)) {
                return response()->json(['error' => 'Invalid decrypted data format'], 400);
            }

            // ✅ Validate decrypted data
            $validator = Validator::make($decryptedData, [
                'cover_letter' => 'required|string|max:255',
                'rate' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'details' => $validator->errors(),
                ], 422);
            }

            // ✅ Pass validated data to the service
            $result = $this->jobApplicationService->submitApplication($validator->validated(), $jobId);

            return response()->json($result, $result['status']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to submit application',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function listAppliedJobs()
    {
        $result = $this->jobApplicationService->listAppliedJobs();
        return isset($result['error'])
            ? response()->json($result, $result['status'])
            : response()->json($result);
    }

    public function getApplicationDetails($applicationId)
    {
        $result = $this->jobApplicationService->getApplicationDetails($applicationId);
        return isset($result['error'])
            ? response()->json($result, $result['status'])
            : response()->json($result);
    }

    public function updateBidStatus(Request $request, $encryptedUserId, $encryptedJobId)
    {
        $userId = EncryptionHelper::decodeId($encryptedUserId);
        $jobId = EncryptionHelper::decodeId($encryptedJobId);
        $encryptedPayload = $request->input('encrypted');
        $decryptedData = json_decode(EncryptionHelper::decodeId($encryptedPayload), true);
        $request->merge($decryptedData);
        $request->validate([
            'status' => 'required|in:Accepted,Rejected',
        ]);

        $result = $this->jobApplicationService->updateBidStatus($request->all(), $userId, $jobId);
        return response()->json($result, $result['status']);
    }

    public function sendNotification()
    {
        broadcast(new NewNotification("New message received!"))->toOthers();
        return response()->json(['message' => 'Notification sent!']);
    }
}
