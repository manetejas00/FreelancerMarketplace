<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::with('client')->where('status', 'open')->get();
        return response()->json($jobs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'budget' => 'required|numeric',
            'category' => 'required|string',
        ]);

        $job = Job::create([
            'client_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'budget' => $request->budget,
            'category' => $request->category,
        ]);

        return response()->json($job, 201);
    }

    public function show(Job $job)
    {
        return response()->json($job->load('client'));
    }

    public function update(Request $request, Job $job)
    {
        if (Auth::id() !== $job->client_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $job->update($request->only(['title', 'description', 'budget', 'category', 'status']));
        return response()->json($job);
    }

    public function destroy(Job $job)
    {
        if (Auth::id() !== $job->client_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $job->delete();
        return response()->json(['message' => 'Job deleted successfully']);
    }

    public function apply(Request $request, $jobId)
    {
        // Ensure the user is authenticated
        $user = Auth::user();  // Retrieve the authenticated user

        // Validate the input (e.g., ensure cover letter is provided)
        $validated = $request->validate([
            'cover_letter' => 'required|string|max:1000',  // Adjust as per your needs
        ]);

        // Find the job by ID
        $job = Job::findOrFail($jobId);  // Return 404 if job not found
        $job->status = 'in_progress';
        $job->save();
        // Attach the user to the job as an applicant (assuming a many-to-many relationship)
        $job->applicants()->attach($user->id, ['cover_letter' => $validated['cover_letter']]);

        // Respond with a success message
        return response()->json(['message' => 'Application submitted successfully!'], 200);
    }
}
