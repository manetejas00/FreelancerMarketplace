<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\FreelancerProfile;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use App\Helpers\EncryptionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    protected ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function index($encodedJobId): JsonResponse
    {
        try {
            $freelancerId = EncryptionHelper::decodeId($encodedJobId);
            $freelancer = FreelancerProfile::findOrFail($freelancerId);
            $data = $this->reviewService->getFreelancerReviews($freelancer);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch reviews', 'details' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request, $encryptedFreelancerId): JsonResponse
    {
        try {
            // 🔓 Decrypt the freelancer ID
            $freelancerId = EncryptionHelper::decodeId($encryptedFreelancerId);
            $freelancer = FreelancerProfile::find($freelancerId);

            if (!$freelancer) {
                return response()->json(['error' => 'Freelancer not found'], 404);
            }

            // 🔹 Ensure the 'encrypted' field is present
            if (!$request->has('encrypted')) {
                return response()->json(['error' => 'Missing encrypted data'], 400);
            }

            // 🔓 Decrypt the encrypted request data
            $decryptedJson = EncryptionHelper::decodeId($request->input('encrypted'));

            // 🔹 Ensure we have valid JSON after decryption
            $decryptedData = json_decode($decryptedJson, true);

            if (!is_array($decryptedData)) {
                return response()->json(['error' => 'Invalid decrypted data format'], 400);
            }

            // 📝 Log for debugging
            logger("Decrypted Review Data:", $decryptedData);

            // Validate decrypted data
            $validatedData = Validator::make($decryptedData, [
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'required|string|max:1000',
            ]);

            if ($validatedData->fails()) {
                return response()->json(['error' => 'Validation failed', 'details' => $validatedData->errors()], 422);
            }

            // Store the review
            $review = $this->reviewService->storeReview($freelancer, $validatedData->validated());

            return response()->json([
                'message' => 'Review submitted successfully',
                'review' => $review
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to submit review',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
