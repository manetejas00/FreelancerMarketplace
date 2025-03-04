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

<template>
    <div class="profile-container">
        <!-- Freelancer Profile Section -->
        <template v-if="user?.roles?.some(role => role.name === 'freelancer')">
            <div class="profile-info">
                <p><strong>Skills:</strong> {{ profile.skills }}</p>
                <p><strong>Experience:</strong> {{ profile.experience }}</p>
                <p><strong>Portfolio:</strong> {{ profile.portfolio }}</p>
                <p><strong>Hourly Rate:</strong> ${{ profile.hourly_rate }}</p>
            </div>
        </template>

        <!-- Client Profile Section -->
        <template v-else-if="user?.roles?.some(role => role.name === 'client')">
            <div class="profile-info">
                <p><strong>Company Name:</strong> {{ profile.company_name }}</p>
                <p><strong>Projects:</strong> {{ profile.projects }}</p>
                <p><strong>Developers:</strong> {{ profile.number_of_developers }}</p>
            </div>
        </template>

        <button @click="isEditing = true" class="edit-button">Edit Profile</button>

        <!-- Edit Profile Section -->
        <div v-if="isEditing" class="edit-profile-form">
            <h3>Edit Profile</h3>

            <div v-if="user?.roles?.some(role => role.name === 'freelancer')" class="form-fields">
                <label>Skills:</label>
                <input v-model="form.skills" class="input-field" />

                <label>Experience:</label>
                <input v-model="form.experience" class="input-field" />

                <label>Portfolio:</label>
                <input v-model="form.portfolio" class="input-field" />

                <label>Hourly Rate:</label>
                <input type="number" v-model="form.hourly_rate" class="input-field" />
            </div>

            <div v-if="user?.roles?.some(role => role.name === 'client')" class="form-fields">
                <label>Company Name:</label>
                <input v-model="form.company_name" class="input-field" />

                <label>Projects:</label>
                <textarea v-model="form.projects" class="input-field"></textarea>

                <label>Number of Developers:</label>
                <input type="number" v-model="form.number_of_developers" class="input-field" />
            </div>

            <div class="form-buttons">
                <button @click="updateProfile" class="save-button">Save</button>
                <button @click="isEditing = false" class="cancel-button">Cancel</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Global Styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Arial', sans-serif;
  background-color: #f4f7fc;
}

.profile-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
}

.profile-info {
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 600px;
  margin-bottom: 20px;
}

.profile-info p {
  font-size: 16px;
  color: #333;
  margin-bottom: 10px;
}

.edit-button {
  background-color: #0056b3;
  color: white;
  padding: 10px 20px;
  border-radius: 4px;
  border: none;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.edit-button:hover {
  background-color: #003d8c;
}

.edit-profile-form {
  background-color: white;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 600px;
}

h3 {
  font-size: 22px;
  font-weight: bold;
  color: #333;
  margin-bottom: 20px;
}

.form-fields {
  margin-bottom: 20px;
}

label {
  font-size: 16px;
  color: #333;
  margin-bottom: 5px;
  display: block;
}

.input-field {
  width: 100%;
  padding: 10px;
  font-size: 14px;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-bottom: 15px;
  box-sizing: border-box;
}

textarea.input-field {
  height: 100px;
  resize: vertical;
}

.form-buttons {
  display: flex;
  gap: 10px;
}

.save-button,
.cancel-button {
  padding: 10px 20px;
  font-size: 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.save-button {
  background-color: #28a745;
  color: white;
}

.save-button:hover {
  background-color: #218838;
}

.cancel-button {
  background-color: #dc3545;
  color: white;
}

.cancel-button:hover {
  background-color: #c82333;
}
</style>
