<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobApplication;
use App\Models\Job;
use App\Models\Bid;
use Illuminate\Support\Facades\Auth;

class JobApplicationController extends Controller
{
    // Apply for a job
    public function apply(Request $request, $jobId)
    {
        // Validate input
        $request->validate([
            'cover_letter' => 'required|string',
            'rate' => 'required|numeric|min:1', // Validate the proposed rate
        ]);

        $job = Job::findOrFail($jobId);

        // Check if the freelancer has already applied for the job
        if (JobApplication::where('freelancer_id', Auth::id())->where('job_id', $jobId)->exists()) {
            return response()->json(['message' => 'You have already applied for this job'], 400);
        }

        // Create the job application record
        $application = JobApplication::create([
            'job_id' => $jobId,
            'freelancer_id' => Auth::id(),
            'cover_letter' => $request->cover_letter,
            'status' => 'pending',
        ]);

        // Create the bid record for the job application
        $bid = Bid::create([
            'job_id' => $jobId,
            'user_id' => Auth::id(),
            'rate' => $request->rate,
            'cover_letter' => $request->cover_letter,
            'status' => 'pending', // The bid is pending until the client accepts it
        ]);

        return response()->json([
            'message' => 'Application and bid submitted successfully',
            'application' => $application,
            'bid' => $bid,
        ]);
    }


    // List applied jobs for freelancer
    public function appliedJobs()
    {
        // Order applications by latest first
        $applications = JobApplication::where('freelancer_id', Auth::id())
            ->with('job') // Include job details with the application
            ->orderBy('created_at', 'desc') // Order by latest application
            ->get();

        return response()->json($applications);
    }

    // Get details of a specific application
    public function show($id)
    {
        // Fetch the latest application for the freelancer
        $application = JobApplication::where('freelancer_id', Auth::id())
            ->where('id', $id)
            ->with('job')
            ->firstOrFail(); // This will ensure it throws an error if not found

        return response()->json($application);
    }
}
