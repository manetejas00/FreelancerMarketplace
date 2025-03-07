<?php

namespace App\Services;

use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class JobService
{
    public function createJob(array $data)
    {
        return Job::create([
            'client_id' => Auth::id(),
            'title' => $data['title'],
            'description' => $data['description'],
            'budget' => $data['budget'],
            'category' => $data['category'],
        ]);
    }

    public function listJobs()
    {
        return Cache::remember('jobs_list', 60, function () {
            $user = Auth::user();

            return Job::with('client:id,name', 'bids:job_id,rate')
                ->latest()
                ->get()
                ->map(function ($job) use ($user) {
                    $job->min_bid = $job->bids->min('rate');
                    $job->max_bid = $job->bids->max('rate');
                    $job->is_creator = $user && $job->client_id === $user->id;
                    return $job;
                })
                ->sortByDesc(fn($job) => $job->is_creator)
                ->values();
        });
    }

    public function getApplicantsWithBids($jobId)
    {
        $job = Job::findOrFail($jobId);

        return $job->appliedUsers()
            ->with(['bids' => fn($query) => $query->where('job_id', $jobId)])
            ->get()
            ->map(fn($user) => $user->setAttribute('bid_amount', $user->bids->first()?->rate));
    }

    public function updateJob($id, array $data)
    {
        $job = Job::findOrFail($id);

        if ($job->client_id !== Auth::id()) {
            throw new Exception('Unauthorized');
        }

        $job->update($data);
        Cache::forget('jobs_list');

        return $job;
    }

    public function deleteJob($id)
    {
        $job = Job::findOrFail($id);

        if ($job->client_id !== Auth::id()) {
            throw new Exception('Unauthorized');
        }

        $job->delete();
        Cache::forget('jobs_list');

        return true;
    }
}
