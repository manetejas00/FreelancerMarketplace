<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Store a new job
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
        ]);

        $job = Job::create([
            'client_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'budget' => $validated['budget'],
            'category' => $validated['category'],
        ]);

        return response()->json(['message' => 'Job posted successfully', 'job' => $job]);
    }

    // Fetch all jobs
    public function index()
    {
        // Fetch jobs with client data
        $jobs = Job::with('client:id,name')
            ->with(['bids' => function ($query) {
                // Fetch bids with rate details
                $query->select('job_id', 'rate');
            }])
            ->latest()
            ->get();

        // Loop through each job to add 'is_creator', 'min_bid' and 'max_bid'
        foreach ($jobs as $job) {
            logger($job->bids);
            $bids = $job->bids;
            $job->min_bid = $bids->isNotEmpty() ? $bids->min('rate') : null;
            $job->max_bid = $bids->isNotEmpty() ? $bids->max('rate') : null;
        }

        // Sort the jobs: first by 'is_creator' (true first), then by 'created_at' (latest first)
        $jobs = $jobs->sortByDesc(function ($job) {
            return $job->is_creator;
        })->values(); // 'values()' is used to reset the keys after sorting

        // Return the jobs data with the 'is_creator', 'min_bid', and 'max_bid' flags
        return response()->json($jobs);
    }




    public function getAppliedUsers($jobId)
    {
        // Fetch the job by ID
        $job = Job::find($jobId);

        if (!$job) {
            return response()->json(['error' => 'Job not found'], 404);
        }

        // Assuming applied_users is a relationship (you may need to adjust this if it's not)
        // Example: if `applied_users` is a pivot table relation (many-to-many)
        $appliedUsers = $job->appliedUsers; // Replace `appliedUsers` with the actual relationship name

        return response()->json($appliedUsers);
    }

    public function update(Request $request, $id)
    {
        $job = Job::findOrFail($id);

        if ($job->client_id !== Auth::id()) {
            return response()->json(['error' => 'You have not created this job profile.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
        ]);

        $job->update($request->all());

        return response()->json($job);
    }

    // Delete a job
    public function destroy($id)
    {

        $job = Job::findOrFail($id);

        if ($job->client_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $job->delete();

        return response()->json(['message' => 'Job deleted successfully']);
    }
}
