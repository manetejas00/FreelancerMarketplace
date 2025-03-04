<template>
    <div>
        <h2 class="text-3xl font-semibold text-gray-800 mb-6">Manage Jobs</h2>
        <button @click="openModal()" class="bg-blue-600 text-white px-5 py-3 rounded-full mb-4 hover:bg-blue-700 transition duration-300 ease-in-out">
            Add Job
        </button>

        <!-- Jobs Table -->
        <table class="job-table">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-sm">Title</th>
                    <th class="px-6 py-3 text-left font-semibold text-sm">Description</th>
                    <th class="px-6 py-3 text-left font-semibold text-sm">Budget</th>
                    <th class="px-6 py-3 text-left font-semibold text-sm">Category</th>
                    <th class="px-6 py-3 text-center font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="job in jobs" :key="job.id" class="border-t hover:bg-gray-100 transition duration-200">
                    <td class="px-6 py-4 text-sm font-medium">{{ job.title }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ job.description }}</td>
                    <td class="px-6 py-4 text-sm text-gray-800">${{ job.budget }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ job.category }}</td>
                    <td class="px-6 py-4 text-center">
                        <button @click="openModal(job)" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition duration-300 ease-in-out">
                            Edit
                        </button>
                        <button @click="deleteJob(job.id)" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-300 ease-in-out ml-2">
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Modal -->
        <div v-if="showModal" class="apply-modal">
            <div class="modal-content">
                <h3 class="text-2xl font-semibold mb-4">{{ editingJob ? "Edit Job" : "Add Job" }}</h3>
                <input v-model="jobForm.title" placeholder="Title" class="w-full p-3 border border-gray-300 rounded-md mb-4 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <textarea v-model="jobForm.description" placeholder="Description" class="w-full p-3 border border-gray-300 rounded-md mb-4 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                <input v-model="jobForm.budget" placeholder="Budget" type="number" class="w-full p-3 border border-gray-300 rounded-md mb-4 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <input v-model="jobForm.category" placeholder="Category" class="w-full p-3 border border-gray-300 rounded-md mb-6 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <div class="flex justify-end">
                    <button @click="saveJob" class="submit-btn hover:bg-green-700 transition duration-300 ease-in-out">
                        Save
                    </button>
                    <button @click="closeModal" class="cancel-btn hover:bg-red-600 transition duration-300 ease-in-out ml-3">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import apiClient from '@/axios';

const jobs = ref([]);
const showModal = ref(false);
const editingJob = ref(null);

const jobForm = ref({
    title: "",
    description: "",
    budget: "",
    category: ""
});

const fetchJobs = async () => {
    try {
        const response = await apiClient.get('/jobs', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        jobs.value = response.data;
    } catch (error) {
        console.error("Error fetching jobs:", error);
    }
};

const saveJob = async () => {
    try {
        if (editingJob.value) {
            await apiClient.put(`/jobs/${editingJob.value.id}`, jobForm.value, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });
        } else {
            await apiClient.post('/jobs', jobForm.value, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });
        }
        fetchJobs();
        closeModal();
    } catch (error) {
        console.error("Error saving job:", error);
    }
};

const deleteJob = async (id) => {
    if (!confirm("Are you sure?")) return;
    try {
        await apiClient.delete(`/jobs/${id}`);
        fetchJobs();
    } catch (error) {
        console.error("Error deleting job:", error);
    }
};

const openModal = (job = null) => {
    editingJob.value = job;
    if (job) {
        jobForm.value = { ...job };
    } else {
        jobForm.value = { title: "", description: "", budget: "", category: "" };
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingJob.value = null;
};

onMounted(fetchJobs);
</script>

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
