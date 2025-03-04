<template>
    <div>
        <!-- Freelancer Profile Section -->
        <template v-if="user?.roles?.some(role => role.name === 'freelancer')">
            <div>
                <p><strong>Skills:</strong> {{ profile.skills }}</p>
                <p><strong>Experience:</strong> {{ profile.experience }}</p>
                <p><strong>Portfolio:</strong> {{ profile.portfolio }}</p>
                <p><strong>Hourly Rate:</strong> ${{ profile.hourly_rate }}</p>
            </div>
        </template>

        <!-- Client Profile Section -->
        <template v-else-if="user?.roles?.some(role => role.name === 'client')">
            <div>
                <p><strong>Company Name:</strong> {{ profile.company_name }}</p>
                <p><strong>Projects:</strong> {{ profile.projects }}</p>
                <p><strong>Developers:</strong> {{ profile.number_of_developers }}</p>
            </div>
        </template>

        <button @click="isEditing = true">Edit Profile</button>

        <!-- Edit Profile Section -->
        <div v-if="isEditing">
            <h3>Edit Profile</h3>

            <div v-if="user?.roles?.some(role => role.name === 'freelancer')">
                <label>Skills:</label>
                <input v-model="form.skills" />

                <label>Experience:</label>
                <input v-model="form.experience" />

                <label>Portfolio:</label>
                <input v-model="form.portfolio" />

                <label>Hourly Rate:</label>
                <input type="number" v-model="form.hourly_rate" />
            </div>

            <div v-if="user?.roles?.some(role => role.name === 'client')">
                <label>Company Name:</label>
                <input v-model="form.company_name" />

                <label>Projects:</label>
                <textarea v-model="form.projects"></textarea>

                <label>Number of Developers:</label>
                <input type="number" v-model="form.number_of_developers" />
            </div>

            <div>
                <button @click="updateProfile">Save</button>
                <button @click="isEditing = false">Cancel</button>
            </div>
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
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
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
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        profile.value = response.data.profile;
        isEditing.value = false;
    } catch (error) {
        console.error('Error updating profile:', error);
    }
};
</script>
