<script setup>
import { ref, onMounted } from "vue";
import apiClient from '@/axios';

const jobs = ref([]);
const applyingJob = ref(null);
const coverLetter = ref("");

const fetchJobs = async () => {
    try {
        const response = await apiClient.get("/jobs", {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });

        jobs.value = response.data;
    } catch (error) {
        console.error("Error fetching jobs:", error);
    }
};

const applyForJob = async (jobId) => {
    try {
        await apiClient.post(`/jobs/${jobId}/apply`,
            { cover_letter: coverLetter.value },
            {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            }
        );
        alert("Application submitted!");
        applyingJob.value = null;
    } catch (error) {
        console.error("Error applying:", error);
    }
};

onMounted(fetchJobs);
</script>

<template>
    <div class="container mx-auto p-6">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Job Listings</h2>

        <!-- Job Table -->
        <div class="overflow-x-auto shadow-lg rounded-lg">
            <table class="min-w-full table-auto border-collapse job-table">
                <thead class="bg-gradient-to-r from-blue-500 to-teal-500 text-white">
                    <tr>
                        <th class="py-3 px-6 text-left text-lg">Job Title</th>
                        <th class="py-3 px-6 text-left text-lg">Description</th>
                        <th class="py-3 px-6 text-left text-lg">Budget</th>
                        <th class="py-3 px-6 text-center text-lg">Status</th>
                        <th class="py-3 px-6 text-center text-lg">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="job in jobs" :key="job.id" class="border-b hover:bg-gray-50 transition-all duration-300">
                        <td class="py-4 px-6 text-sm text-gray-700 font-medium">{{ job.title }}</td>
                        <td class="py-4 px-6 text-sm text-gray-600">{{ job.description }}</td>
                        <td class="py-4 px-6 text-sm text-gray-800 font-bold">${{ job.budget }}</td>
                        <td class="py-4 px-6 text-sm text-gray-800 font-bold">{{ job.status }}</td>
                        <td class="py-4 px-6 text-center">
                            <button @click="applyingJob = job" class="apply-btn">
                                Apply
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Apply Modal -->
        <div v-if="applyingJob" class="apply-modal">
            <div class="modal-content">
                <h3 class="text-xl font-semibold mb-4">Apply for {{ applyingJob.title }}</h3>

                <textarea v-model="coverLetter" placeholder="Enter your cover letter here..." class="cover-letter" rows="5"></textarea>

                <div class="flex justify-between">
                    <button @click="applyForJob(applyingJob.id)" class="submit-btn">
                        Submit Application
                    </button>
                    <button @click="applyingJob = null" class="cancel-btn">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Table Styles */
.job-table {
    width: 100%;
    border-collapse: collapse;
}

.job-table th,
.job-table td {
    padding: 1rem 1.5rem;
    text-align: left;
}

.job-table th {
    background: linear-gradient(90deg, #1e40af, #14b8a6);
    color: white;
    font-size: 1.125rem;
}

.job-table td {
    background-color: #fff;
    font-size: 1rem;
    border-bottom: 1px solid #e0e0e0;
}

/* Table Row Hover */
.job-table tr:hover {
    background-color: #f9fafb;
}

/* Apply Button Styles */
.apply-btn {
    background-color: #2563eb;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    transition: background-color 0.3s ease-in-out;
}

.apply-btn:hover {
    background-color: #1e40af;
}

/* Modal Styles */
.apply-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(31, 41, 55, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 50;
}

.modal-content {
    background-color: white;
    padding: 2rem;
    border-radius: 0.5rem;
    box-shadow: 0px 10px 15px rgba(0, 0, 0, 0.1);
    max-width: 500px;
    width: 100%;
}

/* Textarea Styles */
.cover-letter {
    width: 100%;
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 0.375rem;
    resize: none;
    box-sizing: border-box;
    transition: border 0.3s ease;
}

.cover-letter:focus {
    border-color: #3b82f6;
    outline: none;
}

/* Modal Button Styles */
.submit-btn,
.cancel-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 0.375rem;
    font-weight: 600;
    transition: background-color 0.3s ease-in-out;
}

.submit-btn {
    background-color: #16a34a;
    color: white;
}

.submit-btn:hover {
    background-color: #15803d;
}

.cancel-btn {
    background-color: #ef4444;
    color: white;
}

.cancel-btn:hover {
    background-color: #dc2626;
}
</style>
