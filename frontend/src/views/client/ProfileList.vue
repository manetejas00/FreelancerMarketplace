<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { encryptData } from "@/utils/encryption";

const freelancers = ref([]);
const loading = ref(true);
const showReviewModal = ref(false);
const showJobsModal = ref(false);
const selectedFreelancer = ref(null);
const newReview = ref({ rating: 0, comment: "" });
const submitting = ref(false);

// Fetch freelancers on mount
onMounted(async () => {
    try {
        const { data } = await axios.get("/freelancers");
        freelancers.value = data;
    } catch (error) {
        console.error("Error fetching freelancers:", error);
    } finally {
        loading.value = false;
    }
});

// Open modal and fetch reviews
const openReviewModal = async (freelancer) => {
    selectedFreelancer.value = { ...freelancer, reviews: [] };
    showReviewModal.value = true;

    try {
        const encryptedJobId = encryptData(freelancer.id);
        const { data } = await axios.get(`/review/${encryptedJobId}/freelancers`);
        selectedFreelancer.value.reviews = data.reviews || [];
        selectedFreelancer.value.can_review = data.can_review;
    } catch (error) {
        console.error("Error fetching reviews:", error);
    }
};

// Open modal for applied jobs
const openJobsModal = (freelancer) => {
    selectedFreelancer.value = freelancer;
    showJobsModal.value = true;
};

const closeModals = () => {
    showReviewModal.value = false;
    showJobsModal.value = false;
    selectedFreelancer.value = null;
};

// Submit Review
const submitReview = async () => {
    if (!newReview.value.rating || !newReview.value.comment.trim()) {
        alert("Please provide a rating and a comment.");
        return;
    }

    submitting.value = true;
    try {
        await axios.post(`/freelancers/${selectedFreelancer.value.id}`, newReview.value);
        selectedFreelancer.value.reviews.push({ ...newReview.value });
        newReview.value = { rating: 0, comment: "" };
        alert("Review submitted successfully!");
    } catch (error) {
        console.error("Error submitting review:", error);
        alert("Failed to submit review.");
    } finally {
        submitting.value = false;
    }
};

const generateStars = (rating) => "★".repeat(rating) + "☆".repeat(5 - rating);
</script>

<template>
    <div class="container mt-4">
        <h2>Freelancers List</h2>
        <div v-if="loading">Loading freelancers...</div>
        <table v-else class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Skills</th>
                    <th>Experience</th>
                    <th>Hourly Rate ($)</th>
                    <th>Company</th>
                    <th>Project Details</th>
                    <th>Working Devs</th>
                    <th>Portfolio</th>
                    <th>Reviews</th>
                    <th>Applied Jobs</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="freelancer in freelancers" :key="freelancer.id">
                    <td>{{ freelancer.name }}</td>
                    <td>{{ freelancer.role }}</td>
                    <td>{{ freelancer.skills }}</td>
                    <td>{{ freelancer.experience }} years</td>
                    <td>{{ freelancer.hourly_rate }}</td>
                    <td>{{ freelancer.company_name || 'N/A' }}</td>
                    <td>{{ freelancer.project_details || 'N/A' }}</td>
                    <td>{{ freelancer.working_developers_count ?? 'N/A' }}</td>
                    <td>
                        <a v-if="freelancer.portfolio" :href="freelancer.portfolio" target="_blank">View Portfolio</a>
                        <span v-else>Not Available</span>
                    </td>
                    <td>
                        <button @click="openReviewModal(freelancer)" class="btn btn-primary">View Reviews</button>
                    </td>
                    <td>
                        <button v-if="freelancer.applied_jobs?.length" @click="openJobsModal(freelancer)"
                            class="btn btn-secondary">
                            View More Applied Jobs
                        </button>
                        <span v-else>No applied jobs</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Reviews Modal -->
    <div v-if="showReviewModal" class="modal" @click="closeModals">
        <div class="modal-content" @click.stop>
            <span class="close" @click="closeModals">&times;</span>
            <h2>Reviews for {{ selectedFreelancer?.name }}</h2>

            <ul class="reviews-list" v-if="selectedFreelancer?.can_review == false">
                <li v-for="review in selectedFreelancer?.reviews" :key="review.id" class="review-item">
                    <div class="review-header">
                        <span class="stars" v-html="generateStars(review.rating)"></span>
                        <strong class="rating-text">Rating: {{ review.rating }} / 5</strong>
                    </div>
                    <p class="review-comment">{{ review.comment }}</p>
                </li>
            </ul>

            <div v-if="selectedFreelancer?.can_review" class="review-form">
                <h3 class="form-title">Leave a Review</h3>

                <div class="form-group">
                    <label>Rating:</label>
                    <select v-model="newReview.rating" class="form-control">
                        <option v-for="n in 5" :key="n" :value="n">{{ n }} ★</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Comment:</label>
                    <textarea v-model="newReview.comment" class="form-control textarea"></textarea>
                </div>

                <button @click="submitReview" :disabled="submitting" class="submit-btn">
                    Submit Review
                </button>
            </div>

        </div>
    </div>

    <!-- Applied Jobs Modal -->
    <div v-if="showJobsModal" class="modal" @click="closeModals">
        <div class="modal-content" @click.stop>
            <span class="close" @click="closeModals">&times;</span>
            <h2>Applied Jobs for {{ selectedFreelancer?.name }}</h2>
            <ul class="applied-jobs-list">
                <li v-for="job in selectedFreelancer?.applied_jobs" :key="job.id" class="job-item">
                    <strong>{{ job.title }}</strong> - {{ job.company }}
                </li>
            </ul>
        </div>
    </div>
</template>

<style scoped>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    max-width: 500px;
    width: 100%;
    position: relative;
}

.close {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 30px;
    font-weight: bold;
    cursor: pointer;
}

.review-item,
.job-item {
    background: #f8f9fa;
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 5px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
}

.stars {
    color: #f39c12;
    font-size: 18px;
}

.submit-btn {
    background: #007bff;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    width: 100%;
}

.submit-btn:hover {
    background: #0056b3;
}

.submit-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
}
</style>
