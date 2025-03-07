<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Models\Job;
use App\Models\Bid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class JobApplicationService
{
    public function submitApplication(array $data, $jobId)
    {
        try {
            $userId = Auth::id();

            // Check if user has already applied
            if (JobApplication::where('freelancer_id', $userId)->where('job_id', $jobId)->exists()) {
                return ['error' => 'You have already applied for this job.', 'status' => 400];
            }

            $job = Job::findOrFail($jobId);

            // Start transaction
            DB::beginTransaction();

            // Create job application
            $application = JobApplication::create([
                'job_id' => $job->id,
                'freelancer_id' => $userId,
                'cover_letter' => $data['cover_letter'],
                'status' => 'pending',
            ]);

            // Create bid
            $bid = Bid::create([
                'job_id' => $job->id,
                'user_id' => $userId,
                'rate' => $data['rate'],
                'cover_letter' => $data['cover_letter'],
                'status' => 'pending',
            ]);

            DB::commit();

            return [
                'message' => 'Job application and bid submitted successfully.',
                'application' => $application,
                'bid' => $bid,
                'status' => 201
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Job application submission failed: ' . $e->getMessage());
            return ['error' => 'Failed to submit application. Please try again.', 'status' => 500];
        }
    }

    public function listAppliedJobs()
    {
        try {
            return JobApplication::where('freelancer_id', Auth::id())
                ->with('job')
                ->latest()
                ->get();
        } catch (Exception $e) {
            Log::error('Fetching applied jobs failed: ' . $e->getMessage());
            return ['error' => 'Failed to fetch applied jobs.', 'status' => 500];
        }
    }

    public function getApplicationDetails($applicationId)
    {
        try {
            return JobApplication::where('freelancer_id', Auth::id())
                ->where('id', $applicationId)
                ->with('job')
                ->firstOrFail();
        } catch (Exception $e) {
            Log::error('Fetching job application details failed: ' . $e->getMessage());
            return ['error' => 'Application not found.', 'status' => 404];
        }
    }

    public function updateBidStatus(array $data, $userId, $jobId)
    {
        try {
            $bid = Bid::where('user_id', $userId)->where('job_id', $jobId)->first();

            if (!$bid) {
                return ['error' => 'Bid not found.', 'status' => 404];
            }

            $bid->update(['status' => $data['status']]);

            return [
                'message' => 'Bid status updated successfully.',
                'bid' => $bid,
                'status' => 200
            ];
        } catch (Exception $e) {
            Log::error('Bid status update failed: ' . $e->getMessage());
            return ['error' => 'Failed to update bid status.', 'status' => 500];
        }
    }
}
