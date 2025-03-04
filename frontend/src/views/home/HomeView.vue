<template>
    <div>
        <h2>Welcome, {{ user?.name }}</h2>
        <button v-if="canCreateProject" @click="createProject">Create Project</button>
        <button v-if="canApplyForJob" @click="applyJob">Apply for Job</button>
        <button @click="logout">Logout</button>
        <ProfileView />
    </div>

</template>

<script setup>
import { ref, onMounted } from 'vue';
import apiClient from '@/axios';
import { useRouter } from 'vue-router';
import ProfileView from '../profile/ProfileView.vue';

const user = ref({ permissions: [] }); // Default permissions as an empty array
const canCreateProject = ref(false);
const canApplyForJob = ref(false);
const router = useRouter();

onMounted(async () => {
    try {
        const response = await apiClient.get('/user-profile', {
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });

        user.value = response.data.user || { permissions: [] };

        canCreateProject.value = user.value.permissions.includes('create project');
        canApplyForJob.value = user.value.permissions.includes('apply for job');
    } catch (error) {
        console.error('Error fetching user profile:', error);
    }
});
// Logout function
const logout = async () => {
    try {
        await apiClient.post('/logout', {}, {
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });

        // Clear token and user data
        localStorage.removeItem('token');
        user.value = null;

        // Redirect to login page
        router.push('/login');
    } catch (error) {
        console.error('Logout failed:', error);
    }
};
</script>
