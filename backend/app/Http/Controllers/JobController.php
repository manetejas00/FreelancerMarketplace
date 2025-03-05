<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class JobController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a new job.
     */
    public function createJob(Request $request)
    {
        try {
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

            return response()->json(['message' => 'Job posted successfully', 'job' => $job], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create job', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch all jobs with bid details.
     */
    public function listJobs()
    {
        try {
            $user = Auth::user();

            $jobs = Job::with('client:id,name')
                ->with(['bids' => fn($query) => $query->select('job_id', 'rate')])
                ->latest()
                ->get();

            $jobs->map(function ($job) use ($user) {
                $job->min_bid = $job->bids->min('rate');
                $job->max_bid = $job->bids->max('rate');
                $job->is_creator = $user && $job->client_id === $user->id;
                return $job;
            });

            // Prioritize jobs created by the authenticated user
            $jobs = $jobs->sortByDesc(fn($job) => $job->is_creator)->values();

            return response()->json($jobs);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch jobs', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get applicants and their bids for a job.
     */
    public function getApplicantsWithBids($jobId)
    {
        try {
            $job = Job::findOrFail($jobId);

            $appliedUsers = $job->appliedUsers()
                ->with(['bids' => fn($query) => $query->where('job_id', $jobId)])
                ->get();

            $appliedUsers->map(fn($user) => $user->setAttribute('bid_amount', $user->bids->first()?->rate));

            return response()->json($appliedUsers);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Job not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch applicants', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update job details.
     */
    public function updateJob(Request $request, $id)
    {
        try {
            $job = Job::findOrFail($id);

            if ($job->client_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'budget' => 'required|numeric|min:0',
                'category' => 'required|string|max:100',
            ]);

            $job->update($validated);

            return response()->json(['message' => 'Job updated successfully', 'job' => $job]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Job not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update job', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a job.
     */
    public function deleteJob($id)
    {
        try {
            $job = Job::findOrFail($id);

            if ($job->client_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $job->delete();

            return response()->json(['message' => 'Job deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Job not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete job', 'message' => $e->getMessage()], 500);
        }
    }
}
