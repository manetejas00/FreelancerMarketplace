<?php
namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller {
    // List all jobs
    public function index() {
        return response()->json(Job::with('user')->get());
    }

    // Store a new job (Client only)
    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'budget' => 'required|numeric',
            'category' => 'required|string'
        ]);

        $job = Job::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'budget' => $request->budget,
            'category' => $request->category,
        ]);

        return response()->json($job, 201);
    }

    // Update job (Client only)
    public function update(Request $request, Job $job) {
        $this->authorize('update', $job);

        $job->update($request->only(['title', 'description', 'budget', 'category']));

        return response()->json($job);
    }

    // Delete job (Client only)
    public function destroy(Job $job) {
        $this->authorize('delete', $job);
        $job->delete();
        return response()->json(['message' => 'Job deleted successfully']);
    }
}
