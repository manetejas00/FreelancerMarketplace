<template>
    <div>
        <h2>Manage Jobs</h2>
        <button @click="openModal()">Add Job</button>

        <!-- Jobs Table -->
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Budget</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="job in jobs" :key="job.id">
                    <td>{{ job.title }}</td>
                    <td>{{ job.description }}</td>
                    <td>${{ job.budget }}</td>
                    <td>{{ job.category }}</td>
                    <td>
                        <button @click="openModal(job)">Edit</button>
                        <button @click="deleteJob(job.id)">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Modal -->
        <div v-if="showModal">
            <div>
                <h3>{{ editingJob ? "Edit Job" : "Add Job" }}</h3>
                <input v-model="jobForm.title" placeholder="Title" />
                <textarea v-model="jobForm.description" placeholder="Description"></textarea>
                <input v-model="jobForm.budget" placeholder="Budget" type="number" />
                <input v-model="jobForm.category" placeholder="Category" />
                <div>
                    <button @click="saveJob">Save</button>
                    <button @click="closeModal">Cancel</button>
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
