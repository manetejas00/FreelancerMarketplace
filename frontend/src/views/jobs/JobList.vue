<template>
    <div>
        <h2>Job Listings</h2>
        <div v-if="isClient">
            <h3>Post a Job</h3>
            <input v-model="newJob.title" placeholder="Title" />
            <textarea v-model="newJob.description" placeholder="Description"></textarea>
            <input v-model="newJob.budget" type="number" placeholder="Budget" />
            <input v-model="newJob.category" placeholder="Category" />
            <button @click="postJob">Post</button>
        </div>

        <h3>Available Jobs</h3>
        <ul>
            <li v-for="job in jobs" :key="job.id">
                <h4>{{ job.title }}</h4>
                <p>{{ job.description }}</p>
                <p><strong>Budget:</strong> ${{ job.budget }}</p>
                <p><strong>Category:</strong> {{ job.category }}</p>
                <button v-if="isClient" @click="openEditModal(job)">Edit</button>
                <button v-if="isClient" @click="deleteJob(job.id)">Delete</button>
            </li>
        </ul>

        <div v-if="isEditing" class="modal">
            <h3>Edit Job</h3>
            <input v-model="editJob.title" />
            <textarea v-model="editJob.description"></textarea>
            <input v-model="editJob.budget" type="number" />
            <input v-model="editJob.category" />
            <button @click="updateJob">Save</button>
            <button @click="isEditing = false">Cancel</button>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import apiClient from '@/axios';

const jobs = ref([]);
const newJob = ref({ title: '', description: '', budget: '', category: '' });
const isClient = ref(false);
const isEditing = ref(false);
const editJob = ref({});

const fetchJobs = async () => {
    const response = await axios.get('/jobs');
    jobs.value = response.data;
};

const postJob = async () => {
    await axios.post('/jobs', newJob.value, {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    newJob.value = { title: '', description: '', budget: '', category: '' };
    fetchJobs();
};

const deleteJob = async (id) => {
    await axios.delete(`/jobs/${id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    fetchJobs();
};

const updateJob = async () => {
    await axios.put(`/jobs/${editJob.value.id}`, editJob.value, {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    isEditing.value = false;
    fetchJobs();
};

const openEditModal = (job) => {
    editJob.value = { ...job };
    isEditing.value = true;
};

onMounted(fetchJobs);
</script>
