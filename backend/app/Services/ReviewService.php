<?php

namespace App\Services;

use App\Models\Review;
use App\Models\FreelancerProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ReviewService
{
    public function getFreelancerReviews(FreelancerProfile $freelancer)
    {
        return Cache::remember("freelancer_reviews_{$freelancer->id}", 300, function () use ($freelancer) {
            $reviews = $freelancer->reviews()->with('client')->get();

            $clientReview = null;
            $canReview = true;
            $clientName = null;

            if (Auth::check()) {
                $clientReview = $reviews->where('client_id', Auth::id())->first();
                $canReview = !$clientReview;
                $clientName = Auth::user()->name;
            }

            return [
                'id' => $freelancer->id,
                'name' => $freelancer->name,
                'role' => $freelancer->role,
                'skills' => $freelancer->skills,
                'experience' => $freelancer->experience,
                'hourly_rate' => $freelancer->hourly_rate,
                'portfolio' => $freelancer->portfolio,
                'company_name' => $freelancer->company_name,
                'project_details' => $freelancer->project_details,
                'working_developers_count' => $freelancer->working_developers_count,
                'applied_jobs' => $freelancer->applied_jobs,
                'reviews' => $reviews->map(fn($review) => [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'client' => [
                        'id' => $review->client->id,
                        'name' => $review->client->name,
                    ],
                ]),
                'can_review' => $canReview,
                'client_name' => $clientName,
                'client_rating' => $clientReview ? $clientReview->rating : null
            ];
        });
    }

    public function storeReview(FreelancerProfile $freelancer, array $data)
    {
        $review = Review::create([
            'client_id' => Auth::id(),
            'freelancer_id' => $freelancer->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'],
        ]);

        Cache::forget("freelancer_reviews_{$freelancer->id}"); // Clear cache after adding a review

        return $review->load('client');
    }
}
