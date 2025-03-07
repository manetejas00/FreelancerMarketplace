<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\FreelancerProfile;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    protected ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function index(FreelancerProfile $freelancer): JsonResponse
    {
        try {
            $data = $this->reviewService->getFreelancerReviews($freelancer);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch reviews', 'details' => $e->getMessage()], 500);
        }
    }

    public function store(StoreReviewRequest $request, FreelancerProfile $freelancer): JsonResponse
    {
        try {
            $review = $this->reviewService->storeReview($freelancer, $request->validated());

            return response()->json([
                'message' => 'Review submitted successfully',
                'review' => $review
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to submit review', 'details' => $e->getMessage()], 500);
        }
    }
}
