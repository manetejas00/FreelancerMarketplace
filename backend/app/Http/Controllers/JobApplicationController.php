<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobApplication;
use App\Models\Job;
use App\Models\Bid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Events\NewNotification;

class JobApplicationController extends Controller
{
    /**
     * Submit a job application with a bid.
     */
    public function submitApplication(Request $request, $jobId)
    {
        try {
            $request->validate([
                'cover_letter' => 'required|string|max:2000',
                'rate' => 'required|numeric|min:1|max:10000',
            ]);

            $userId = Auth::id();

            // Check if user has already applied
            if (JobApplication::where('freelancer_id', $userId)->where('job_id', $jobId)->exists()) {
                return response()->json(['message' => 'You have already applied for this job.'], 400);
            }

            $job = Job::findOrFail($jobId);

            // Start transaction to ensure both records are created together
            \DB::beginTransaction();

            // Create job application
            $application = JobApplication::create([
                'job_id' => $job->id,
                'freelancer_id' => $userId,
                'cover_letter' => $request->cover_letter,
                'status' => 'pending',
            ]);

            // Create bid
            $bid = Bid::create([
                'job_id' => $job->id,
                'user_id' => $userId,
                'rate' => $request->rate,
                'cover_letter' => $request->cover_letter,
                'status' => 'pending',
            ]);

            \DB::commit(); // Commit transaction

            return response()->json([
                'message' => 'Job application and bid submitted successfully.',
                'application' => $application,
                'bid' => $bid,
            ], 201);
        } catch (Exception $e) {
            \DB::rollBack(); // Rollback on error
            Log::error('Job application submission failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to submit application. Please try again.'], 500);
        }
    }

    /**
     * Get a list of jobs the freelancer has applied for.
     */
    public function listAppliedJobs()
    {
        try {
            $applications = JobApplication::where('freelancer_id', Auth::id())
                ->with('job')
                ->latest()
                ->get();

            return response()->json($applications);
        } catch (Exception $e) {
            Log::error('Fetching applied jobs failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch applied jobs.'], 500);
        }
    }

    /**
     * Retrieve details of a specific job application.
     */
    public function getApplicationDetails($applicationId)
    {
        try {
            $application = JobApplication::where('freelancer_id', Auth::id())
                ->where('id', $applicationId)
                ->with('job')
                ->firstOrFail();

            return response()->json($application);
        } catch (Exception $e) {
            Log::error('Fetching job application details failed: ' . $e->getMessage());
            return response()->json(['error' => 'Application not found.'], 404);
        }
    }

    /**
     * Update the status of a bid.
     */
    public function updateBidStatus(Request $request, $userId, $jobId)
    {
        try {
            $request->validate([
                'status' => 'required|in:Accepted,Rejected',
            ]);

            // Find the bid
            $bid = Bid::where('user_id', $userId)->where('job_id', $jobId)->first();

            // If no bid found, return an error
            if (!$bid) {
                return response()->json(['error' => 'Bid not found.'], 404);
            }

            // Update the bid status
            $bid->update(['status' => $request->status]);

            return response()->json([
                'message' => 'Bid status updated successfully.',
                'bid' => $bid,
            ]);
        } catch (Exception $e) {
            Log::error('Bid status update failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update bid status.'], 500);
        }
    }


    public function sendNotification()
    {
        broadcast(new NewNotification("New message received!"))->toOthers();
        return response()->json(['message' => 'Notification sent!']);
    }
}
