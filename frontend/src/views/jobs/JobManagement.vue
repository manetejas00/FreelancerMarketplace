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
        const response =
            fetchJobs();
        closeModal();
    } catch (error) {
        console.error("Error saving job:", error);
    }
};

const deleteJob = async (id) => {
    if (!confirm("Are you sure?")) return;
    try {
        await axios.delete(`/api/jobs/${id}`);
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
        <h2 class="text-lg font-bold">Manage Jobs</h2>
        <button @click="openModal()" class="bg-blue-500 text-white px-3 py-1 rounded mb-3">
            Add Job
        </button>
        <ul>
            <li v-for="job in jobs" :key="job.id" class="border p-4 mt-2 flex justify-between">
                <div>
                    <h3 class="font-semibold">{{ job.title }}</h3>
                    <p>{{ job.description }}</p>
                    <p>Budget: ${{ job.budget }}</p>
                </div>
                <div>
                    <button @click="openModal(job)" class="bg-yellow-500 text-white px-3 py-1 rounded mx-1">
                        Edit
                    </button>
                    <button @click="deleteJob(job.id)" class="bg-red-500 text-white px-3 py-1 rounded">
                        Delete
                    </button>
                </div>
            </li>
        </ul>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
            <div class="bg-white p-5 rounded shadow-lg w-1/3">
                <h3 class="text-lg">{{ editingJob ? "Edit Job" : "Add Job" }}</h3>
                <input v-model="jobForm.title" placeholder="Title" class="w-full p-2 border my-2" />
                <textarea v-model="jobForm.description" placeholder="Description"
                    class="w-full p-2 border my-2"></textarea>
                <input v-model="jobForm.budget" placeholder="Budget" type="number" class="w-full p-2 border my-2" />
                <input v-model="jobForm.category" placeholder="Category" class="w-full p-2 border my-2" />
                <button @click="saveJob" class="bg-green-500 text-white px-3 py-1 mt-2 rounded">
                    Save
                </button>
                <button @click="closeModal" class="bg-gray-500 text-white px-3 py-1 mt-2 rounded">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>
