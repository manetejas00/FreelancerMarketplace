<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\FreelancerProfile;

class ReviewController extends Controller
{
    public function index(FreelancerProfile $freelancer)
    {
        $reviews = $freelancer->reviews()->with('client')->get();

        $clientReview = null;
        $canReview = true;

        if (auth()->check()) {
            $clientReview = $reviews->where('client_id', auth()->id())->first();
            $canReview = !$clientReview;
            $clientName = auth()->user()->name;
        }

        // Transform the reviews without `can_review`
        $reviewsWithMeta = $reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'client' => [
                    'id' => $review->client->id,
                    'name' => $review->client->name,
                ],
            ];
        });

        return response()->json([
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
            'reviews' => $reviewsWithMeta,
            'can_review' => $canReview, // ✅ Placing it at the freelancer level
            'client_name' => $clientName,
            'client_rating' => $clientReview ? $clientReview->rating : null
        ]);
    }




    public function store(Request $request, FreelancerProfile $freelancer)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $review = new Review([
            'client_id' => auth()->id(),
            'freelancer_id' => $freelancer->id, // Explicitly set freelancer_id
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $review->save();

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => $review->load('client')
        ], 201);
    }
}
