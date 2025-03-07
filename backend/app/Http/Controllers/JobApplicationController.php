<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitApplicationRequest;
use App\Services\JobApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Events\NewNotification;

class JobApplicationController extends Controller
{
    protected $jobApplicationService;

    public function __construct(JobApplicationService $jobApplicationService)
    {
        $this->jobApplicationService = $jobApplicationService;
    }

    public function submitApplication(SubmitApplicationRequest $request, $jobId)
    {
        $result = $this->jobApplicationService->submitApplication($request->validated(), $jobId);
        return response()->json($result, $result['status']);
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

    public function updateBidStatus(Request $request, $userId, $jobId)
    {
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
