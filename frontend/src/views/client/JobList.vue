<template>
    <div class="container mt-4">
        <h2 class="text-center text-primary mb-4">Available Jobs</h2>

        <div v-if="loading" class="text-center">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Loading jobs...
        </div>
        <div v-else-if="jobs.length === 0" class="text-center">No jobs available.</div>
        <div v-else>
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
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
                            <button class="btn btn-sm btn-warning me-2" v-if="job.is_creator"
                                @click="openEditModal(job)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger" v-if="job.is_creator" @click="deleteJob(job.id)">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                            <button class="btn btn-sm btn-info mt-2" @click="openAppliedUsersModal(job)">
                                <i class="fas fa-users"></i> View Applied Users
                            </button>
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
                        <label for="title">Title</label>
                        <input id="title" v-model="editJobData.title" class="form-control" required />
                    </div>

                    <div class="mb-3">
                        <label for="description">Description</label>
                        <textarea id="description" v-model="editJobData.description" class="form-control"
                            required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="budget">Budget ($)</label>
                        <input id="budget" type="number" v-model="editJobData.budget" class="form-control" step="0.01"
                            min="0" required />
                    </div>


                    <div class="mb-3">
                        <label for="category">Category</label>
                        <select id="category" v-model="editJobData.category" class="form-control" required>
                            <option v-for="category in categories" :key="category" :value="category">{{ category }}
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
                    <li v-for="user in appliedUsers" :key="user.id"
                        class="list-group-item d-flex justify-content-between align-items-center"
                        @click="openAppliedUsersDetails(user)" style="cursor: pointer;">
                        {{ user.name }}
                        <span class="badge bg-primary rounded-pill">${{ user.bid_amount }}</span>
                    </li>
                </ul>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" @click="closeAppliedUsersModal">Close</button>
                </div>
            </div>
        </div>

        <!-- User Details Modal -->
        <div v-if="showUserDetailsModal" class="modal-backdrop">
            <div class="modal-content">
                <h3>User Details</h3>
                <div v-if="selectedUser">
                    <p><strong>Name:</strong> {{ selectedUser.name }}</p>
                    <p><strong>Email:</strong> {{ selectedUser.email }}</p>
                    <p><strong>Bid Amount:</strong> ${{ selectedUser.bid_amount }}</p>
                    <p><strong>Experience:</strong> {{ selectedUser.experience || "N/A" }}</p>
                    <p><strong>Skills:</strong> {{ selectedUser.skills || "N/A" }}</p>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" @click="closeUserDetailsModal">Close</button>
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
const appliedUsers = ref([]);

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

const openEditModal = (job) => {
    editJobData.value = { ...job };
    showModal.value = true;
};

const closeEditModal = () => {
    showModal.value = false;
};

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

        if (response.data.error) {
            alert(response.data.error);
        } else {
            const index = jobs.value.findIndex((j) => j.id === editJobData.value.id);
            if (index !== -1) {
                jobs.value[index] = { ...editJobData.value };
            }

            closeEditModal();
        }
    } catch (error) {
        if (error.response && error.response.data && error.response.data.error) {
            alert(error.response.data.error);
        } else {
            alert("Failed to update the job. Please try again.");
        }
    }
};

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

const openAppliedUsersModal = async (job) => {
    try {
        const response = await axios.get(`/jobs/${job.id}/applicants`, {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        appliedUsers.value = response.data;
        showAppliedUsersModal.value = true;
    } catch (error) {
        console.error("Error fetching applied users:", error);
    }
};
const openAppliedUsersDetails = async (user) => {
    try {
        const response = await axios.get(`/users/${user.id}/applied-users-details`, {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        selectedUser.value = response.data.data; // Correct assignment
        showUserDetailsModal.value = true;
    } catch (error) {
        console.error("Error fetching applied user details:", error);
    }
};



const closeAppliedUsersModal = () => {
    showAppliedUsersModal.value = false;
};

const showUserDetailsModal = ref(false);
const selectedUser = ref(null);

const openUserDetailsModal = (user) => {
    selectedUser.value = user;
    showUserDetailsModal.value = true;
};

const closeUserDetailsModal = () => {
    showUserDetailsModal.value = false;
};

</script>

<style scoped>
/* Global Styles */
.container {
    max-width: 1200px;
    margin: 0 auto;
}

h2 {
    font-family: 'Arial', sans-serif;
    font-weight: 700;
    color: #2c3e50;
}

/* Table Styles */
.table {
    border-radius: 8px;
    border: 1px solid #ddd;
}

.table-striped tbody tr:nth-child(odd) {
    background-color: #f9f9f9;
}

.table-hover tbody tr:hover {
    background-color: #f1f1f1;
    cursor: pointer;
}

th,
td {
    text-align: center;
}

/* Modal Styles */
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    width: 500px;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
}

.modal-actions button {
    margin-left: 10px;
}

/* Applied Users Styles */
.list-group-item {
    display: flex;
    justify-content: space-between;
    padding: 12px;
    border: 1px solid #ddd;
    margin-bottom: 8px;
}

.list-group-item:hover {
    background-color: #f9f9f9;
}

.badge {
    font-size: 1rem;
}

/* Button Styling */
.btn-info {
    background-color: #17a2b8;
    color: white;
}

.btn-info:hover {
    background-color: #138496;
}

.btn-warning {
    background-color: #ffc107;
    color: white;
}

.btn-warning:hover {
    background-color: #e0a800;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
}

/* Input and Select Styles */
input.form-control,
textarea.form-control,
select.form-control {
    border-radius: 5px;
    margin-bottom: 15px;
}
</style>
