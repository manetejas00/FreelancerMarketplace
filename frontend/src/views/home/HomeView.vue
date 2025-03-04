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

<template>
  <!-- Navigation Bar -->
    <nav >
      <RouterLink to="/" class="nav-link">Home</RouterLink>
      <RouterLink to="/about" class="nav-link">About</RouterLink>
      <RouterLink v-if="user?.roles?.join(', ') === 'client'" to="/manage-jobs" class="nav-link">Manage Jobs</RouterLink>
      <RouterLink v-if="user?.roles?.join(', ') === 'freelancer'" to="/jobs" class="nav-link">Job Listings</RouterLink>
      <button @click="logout" class="logout-button">Logout</button>
    </nav>
  <!-- Profile Section -->
  <div class="profile-container">
    <div class="profile-card">
      <h2 class="profile-title">Welcome, {{ user?.name }} ({{ user?.roles?.join(', ') }})</h2>
      <ProfileView />
    </div>
  </div>
</template>

<style scoped>
/* Global Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: Arial, sans-serif;
  background-color: #f4f7fc;
  color: #333;
}

/* Navigation Bar Styles */
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #0056b3;
  padding: 15px 30px;
  color: white;
}

.navbar .logo {
  font-size: 24px;
  font-weight: bold;
}

.navbar .logo-text {
  color: white;
  text-decoration: none;
}

.navbar .nav-links {
  display: flex;
  gap: 20px;
  align-items: center;
}

.navbar .nav-link {
  font-size: 16px;
  color: white;
  text-decoration: none;
  transition: color 0.3s ease;
}

.navbar .nav-link:hover {
  color: #ffc107;
}

.logout-button {
  padding: 10px 20px;
  background-color: #ff4c4c;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s ease;
}

.logout-button:hover {
  background-color: #e04343;
}

/* Profile Section Styles */
.profile-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  padding: 20px;
}

.profile-card {
  background-color: white;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 500px;
  text-align: center;
}

.profile-title {
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 20px;
}
</style>
