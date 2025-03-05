<template>
    <div class="container mt-4">
        <h2>Available Jobs</h2>

        <div v-if="loading" class="text-center">Loading jobs...</div>
        <div v-else-if="jobs.length === 0" class="text-center">No jobs available.</div>
        <div v-else>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Budget ($)</th>
                        <th>Category</th>
                        <th>Posted By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(job, index) in jobs" :key="job.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ job.title }}</td>
                        <td>{{ job.description }}</td>
                        <td>${{ job.budget }}</td>
                        <td>{{ job.category }}</td>
                        <td>{{ job.client?.name || "Unknown" }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning me-2" v-if="job.is_creator" @click="openEditModal(job)">Edit</button>
                            <button class="btn btn-sm btn-danger" @click="deleteJob(job.id)">Delete</button>
                            <!-- Show applied users in popup -->
                            <button class="btn btn-sm btn-info" @click="openAppliedUsersModal(job)">View Applied
                                Users</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Edit Job Modal -->
        <div v-if="showModal" class="modal-backdrop">
            <div class="modal-content">
                <h3>Edit Job</h3>
                <form @submit.prevent="updateJob">
                    <div class="mb-3">
                        <label>Title</label>
                        <input v-model="editJobData.title" class="form-control" required />
                    </div>

                    <div class="mb-3">
                        <label>Description</label>
                        <textarea v-model="editJobData.description" class="form-control" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label>Budget ($)</label>
                        <input type="number" v-model="editJobData.budget" class="form-control" required />
                    </div>

                    <div class="mb-3">
                        <label>Category</label>
                        <select v-model="editJobData.category" class="form-control" required>
                            <option v-for="category in categories" :key="category" :value="category">
                                {{ category }}
                            </option>
                        </select>
                    </div>

                    <div class="modal-actions">
                        <button type="submit" class="btn btn-success">Update</button>
                        <button type="button" class="btn btn-secondary" @click="closeEditModal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Applied Users Modal -->
        <div v-if="showAppliedUsersModal" class="modal-backdrop">
            <div class="modal-content">
                <h3>Applied Users</h3>
                <ul class="list-group">
                    <li v-for="user in appliedUsers" :key="user.id" class="list-group-item">
                        {{ user.name }}
                    </li>
                </ul>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" @click="closeAppliedUsersModal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const jobs = ref([]);
const loading = ref(true);
const showModal = ref(false);
const showAppliedUsersModal = ref(false);
const categories = ["Web Development", "Design", "Marketing", "Writing"];
const editJobData = ref({ title: "", description: "", budget: "", category: "", id: null });
const appliedUsers = ref([]);  // For storing applied users

onMounted(async () => {
    await fetchJobs();
});

const fetchJobs = async () => {
    try {
        const response = await axios.get("/jobs", {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        jobs.value = response.data;
    } catch (error) {
        console.error("Error fetching jobs:", error);
    } finally {
        loading.value = false;
    }
};

// Open Modal for Editing
const openEditModal = (job) => {
    editJobData.value = { ...job }; // Clone job data into ref
    showModal.value = true;
};

// Close Edit Modal
const closeEditModal = () => {
    showModal.value = false;
};

// Update Job
const getAuthToken = () => localStorage.getItem("token");

const updateJob = async () => {
    const token = getAuthToken();
    if (!token) {
        alert('You are not authenticated');
        return;
    }

    try {
        const response = await axios.put(`/jobs/${editJobData.value.id}`, editJobData.value, {
            headers: { Authorization: `Bearer ${token}` },
        });

        // Check if the server returned an error message
        if (response.data.error) {
            alert(response.data.error); // Display the error message returned from the server
        } else {
            // Update job in the list if update is successful
            const index = jobs.value.findIndex((j) => j.id === editJobData.value.id);
            if (index !== -1) {
                jobs.value[index] = { ...editJobData.value };
            }

            closeEditModal();
        }
    } catch (error) {
        // Check if error.response and error.response.data exist to prevent accessing undefined properties
        if (error.response && error.response.data && error.response.data.error) {
            alert(error.response.data.error); // Show the error message from the response
        } else {
            alert("Failed to update the job. Please try again."); // Fallback error message
        }
    }
};

// Delete Job
const deleteJob = async (id) => {
    if (confirm("Are you sure you want to delete this job?")) {
        try {
            await axios.delete(`/jobs/${id}`, {
                headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
            });
            jobs.value = jobs.value.filter(job => job.id !== id);
        } catch (error) {
            console.error("Error deleting job:", error);
        }
    }
};

// Open Modal to View Applied Users
const openAppliedUsersModal = async (job) => {
    try {
        const response = await axios.get(`/jobs/${job.id}/applied-users`, {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        appliedUsers.value = response.data;
        showAppliedUsersModal.value = true;
    } catch (error) {
        console.error("Error fetching applied users:", error);
    }
};

// Close Applied Users Modal
const closeAppliedUsersModal = () => {
    showAppliedUsersModal.value = false;
};
</script>

<style scoped>
/* Modal Styles */
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    width: 500px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.modal-actions {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
}

.modal-content h3 {
    font-size: 1.5rem;
    margin-bottom: 20px;
}

/* Applied Users Modal */
.list-group-item {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border: 1px solid #ddd;
    margin-bottom: 5px;
}

.list-group-item:hover {
    background-color: #f0f0f0;
    cursor: pointer;
}

/* Buttons */
.btn-info {
    background-color: #17a2b8;
    color: white;
}

.btn-info:hover {
    background-color: #138496;
}

/* Modal Form Inputs */
input.form-control,
textarea.form-control,
select.form-control {
    border-radius: 5px;
    margin-bottom: 15px;
}
</style>
