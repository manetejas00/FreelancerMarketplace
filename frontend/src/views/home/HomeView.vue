<template>
    <!-- Navigation Bar -->
    <nav>
        <RouterLink to="/" class="nav-link">Home</RouterLink>
        <RouterLink v-if="user?.roles?.join(', ') === 'client'" to="/manage-jobs" class="nav-link">Manage Jobs
        </RouterLink>
        <RouterLink v-if="user?.roles?.join(', ') === 'freelancer'" to="/jobs" class="nav-link">Job Listings
        </RouterLink>
        <button @click="logout" class="logout-button">Logout</button>
    </nav>
    <!-- Profile Section -->
    <div>
        <div>
            <h2>Welcome, {{ user?.name }} ({{ user?.roles?.join(', ') }})</h2>
            <ProfileView />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import apiClient from '@/axios';
import { useRouter } from 'vue-router';
import ProfileView from '../profile/ProfileView.vue';

const user = ref(null);
const router = useRouter();

onMounted(async () => {
    try {
        const response = await apiClient.get('/user-profile', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        user.value = response.data.user || { permissions: [] };
    } catch (error) {
        console.error('Error fetching user profile:', error);
    }
});

// Logout function
const logout = async () => {
    try {
        await apiClient.post('/logout', {}, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        // Clear token and user data
        localStorage.removeItem('token');
        localStorage.removeItem('roles');
        user.value = null;

        // Redirect to login page
        router.push('/login');
    } catch (error) {
        console.error('Logout failed:', error);
    }
};
</script>
