<template>
    <div>
        <h2>Profile</h2>
        <p><strong>Name:</strong> {{ user?.name }} ({{user?.roles?.map(role => role.name).join(', ') || 'No Role'}})
        </p>
        <template v-if="user?.roles?.some(role => role.name === 'freelancer')">
            <p><strong>Skills:</strong> {{ profile.skills }}</p>
            <p><strong>Experience:</strong> {{ profile.experience }}</p>
            <p><strong>Portfolio:</strong> {{ profile.portfolio }}</p>
            <p><strong>Hourly Rate:</strong> ${{ profile.hourly_rate }}</p>
        </template>

        <template v-else-if="user?.roles?.some(role => role.name === 'client')">
            <p><strong>Company Name:</strong> {{ profile.company_name }}</p>
            <p><strong>Projects:</strong> {{ profile.projects }}</p>
            <p><strong>Developers:</strong> {{ profile.number_of_developers }}</p>
        </template>

        <button @click="isEditing = true">Edit Profile</button>

        <div v-if="isEditing ">
            <h3>Edit Profile</h3>

            <div v-if="user?.roles?.some(role => role.name === 'freelancer')">
                <label>Skills: <input v-model="form.skills" /></label>
                <label>Experience: <input v-model="form.experience" /></label>
                <label>Portfolio: <input v-model="form.portfolio" /></label>
                <label>Hourly Rate: <input type="number" v-model="form.hourly_rate" /></label>
            </div>

            <div v-if="user?.roles?.some(role => role.name === 'client')">
                <label>Company Name: <input v-model="form.company_name" /></label>
                <label>Projects: <textarea v-model="form.projects"></textarea></label>
                <label>Number of Developers: <input type="number" v-model="form.number_of_developers" /></label>
            </div>

            <button @click="updateProfile">Save</button>
            <button @click="isEditing = false">Cancel</button>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import apiClient from '@/axios';

const user = ref(null);
const profile = ref({});
const isEditing = ref(false);
const form = ref({});

onMounted(async () => {
    try {
        const response = await apiClient.get('/profile', {
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });

        user.value = response.data.user;
        profile.value = response.data.profile || {};

        form.value = { ...profile.value };
    } catch (error) {
        console.error('Error fetching profile:', error);
    }
});

const updateProfile = async () => {
    try {
        const response = await apiClient.post('/profile/update', form.value, {
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });

        profile.value = response.data.profile;
        isEditing.value = false;
    } catch (error) {
        console.error('Error updating profile:', error);
    }
};
</script>
