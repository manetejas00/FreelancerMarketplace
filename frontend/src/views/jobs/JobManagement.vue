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

<template>
    <div>
        <h2 class="text-3xl font-semibold text-gray-800 mb-6">Manage Jobs</h2>
        <button @click="openModal()" class="bg-blue-600 text-white px-5 py-3 rounded-full mb-4 hover:bg-blue-700 transition duration-300 ease-in-out">
            Add Job
        </button>

        <!-- Jobs Table -->
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
            <thead class="bg-blue-600 text-white">
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
        <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-8 rounded-xl shadow-xl w-1/2 max-w-lg">
                <h3 class="text-2xl font-semibold mb-4">{{ editingJob ? "Edit Job" : "Add Job" }}</h3>
                <input v-model="jobForm.title" placeholder="Title" class="w-full p-3 border border-gray-300 rounded-md mb-4 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <textarea v-model="jobForm.description" placeholder="Description" class="w-full p-3 border border-gray-300 rounded-md mb-4 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                <input v-model="jobForm.budget" placeholder="Budget" type="number" class="w-full p-3 border border-gray-300 rounded-md mb-4 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <input v-model="jobForm.category" placeholder="Category" class="w-full p-3 border border-gray-300 rounded-md mb-6 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <div class="flex justify-end">
                    <button @click="saveJob" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-300 ease-in-out">
                        Save
                    </button>
                    <button @click="closeModal" class="bg-gray-500 text-white px-6 py-3 rounded-lg ml-3 hover:bg-gray-600 transition duration-300 ease-in-out">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    background-color: #1e40af;
}

th, td {
    padding: 16px;
    text-align: left;
}

tbody tr:nth-child(even) {
    background-color: #f9fafb;
}

tbody tr:hover {
    background-color: #f1f5f9;
}

button {
    transition: background-color 0.3s ease;
}

button:hover {
    cursor: pointer;
}

/* Modal Styles */
input, textarea {
    border-radius: 8px;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
}

input:focus, textarea:focus {
    box-shadow: 0 0 8px rgba(29, 78, 216, 0.5);
}

textarea {
    resize: vertical;
}

.bg-black.bg-opacity-50 {
    opacity: 0.75 !important;
}

/* Button hover effects */
button:hover {
    cursor: pointer;
    opacity: 0.9;
}
</style>
