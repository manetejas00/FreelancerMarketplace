<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const freelancers = ref([]);
const loading = ref(true);
const showModal = ref(false);
const selectedFreelancer = ref(null); // Store the selected freelancer for the modal

onMounted(async () => {
    try {
        const response = await axios.get("/freelancers"); // Ensure correct API route
        freelancers.value = response.data;
    } catch (error) {
        console.error("Error fetching freelancers:", error);
    } finally {
        loading.value = false;
    }
});

// Function to open the modal with the freelancer's applied jobs
const openModal = (freelancer) => {
    selectedFreelancer.value = freelancer;
    showModal.value = true;
};

// Function to close the modal
const closeModal = () => {
    showModal.value = false;
};
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
                        <a v-if="freelancer.portfolio" :href="freelancer.portfolio" target="_blank">
                            View Portfolio
                        </a>
                        <span v-else>Not Available</span>
                    </td>
                    <td>
                        <ul>
                            <li v-if="freelancer.applied_jobs.length === 0">No applied jobs</li>
                            <li v-if="freelancer.applied_jobs.length > 0">
                                <button @click="openModal(freelancer)" class="view-more-btn">
                                    View More Applied Jobs  
                                </button>
                            </li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal to show the full list of applied jobs -->
    <div v-if="showModal" class="modal" @click="closeModal">
        <div class="modal-content" @click.stop>
            <span class="close" @click="closeModal">&times;</span>
            <h2>Applied Jobs for {{ selectedFreelancer?.name }}</h2>
            <ul>
                <li v-for="job in selectedFreelancer?.applied_jobs" :key="job.id">
                    <a :href="`/jobs/${job.id}`" target="_blank">{{ job.title }}</a>
                </li>
            </ul>
        </div>
    </div>
</template>

<style scoped>
/* Modal styling */
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
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    max-width: 500px;
    width: 100%;
}

.close {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 30px;
    font-weight: bold;
    cursor: pointer;
}

.view-more-btn {
    background-color: #4CAF50;
    /* Green background */
    color: white;
    /* White text */
    border: none;
    /* Remove border */
    padding: 10px 20px;
    /* Add some padding */
    font-size: 16px;
    /* Set font size */
    cursor: pointer;
    /* Change cursor on hover */
    border-radius: 5px;
    /* Rounded corners */
    transition: background-color 0.3s ease, transform 0.2s ease;
    /* Smooth transition effects */
}

/* Hover effect */
.view-more-btn:hover {
    background-color: #45a049;
    /* Darker green on hover */
    transform: scale(1.05);
    /* Slightly enlarge the button */
}

/* Focus effect */
.view-more-btn:focus {
    outline: none;
    /* Remove outline */
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    /* Blue glow on focus */
}
</style>
