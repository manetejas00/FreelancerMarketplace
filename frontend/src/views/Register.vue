<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import apiClient from '../axios';

const router = useRouter();
const name = ref('');
const email = ref('');
const password = ref('');
const password_confirmation = ref('');
const role = ref('freelancer');
const errorMessage = ref('');

const register = async () => {
    try {
        const response = await apiClient.post('/register', {
            name: name.value,
            email: email.value,
            password: password.value,
            password_confirmation: password_confirmation.value,
            role: role.value
        });

        localStorage.setItem('token', response.data.token);
        localStorage.setItem('roles', response.data.roles);
        router.push('/dashboard');
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Registration failed';
    }
};
</script>

<template>
  <div class="register-container">
    <div class="register-card">
      <h2 class="register-title">Register</h2>
      <form @submit.prevent="register">
        <div class="input-group">
          <input v-model="name" placeholder="Name" required class="input-field" />
        </div>
        <div class="input-group">
          <input v-model="email" type="email" placeholder="Email" required class="input-field" />
        </div>
        <div class="input-group">
          <input v-model="password" type="password" placeholder="Password" required class="input-field" />
        </div>
        <div class="input-group">
          <input v-model="password_confirmation" type="password" placeholder="Confirm Password" required class="input-field" />
        </div>
        <div class="input-group">
          <select v-model="role" class="input-field">
            <option value="freelancer">Freelancer</option>
            <option value="client">Client</option>
          </select>
        </div>
        <button type="submit" class="register-button">Register</button>
        <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
      </form>
    </div>
  </div>
</template>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  background-color: #f4f7fc;
  font-family: Arial, sans-serif;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
}

.register-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.register-card {
  background-color: white;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
  text-align: center;
}

.register-title {
  font-size: 24px;
  font-weight: bold;
  color: #333;
  margin-bottom: 20px;
}

.input-group {
  margin-bottom: 15px;
}

.input-field {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 16px;
  color: #333;
  transition: border-color 0.3s;
}

.input-field:focus {
  border-color: #0056b3;
  outline: none;
}

.register-button {
  width: 100%;
  padding: 12px;
  background-color: #0056b3;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.register-button:hover {
  background-color: #00408d;
}

.error-message {
  color: red;
  font-size: 14px;
  margin-top: 15px;
}
</style>
