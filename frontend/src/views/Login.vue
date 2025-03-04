<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import apiClient from '../axios';

const router = useRouter();
const email = ref('');
const password = ref('');
const errorMessage = ref('');

const login = async () => {
    try {
        const response = await apiClient.post('/login', {
            email: email.value,
            password: password.value
        });

        localStorage.setItem('token', response.data.token);
        localStorage.setItem('roles', response.data.roles);
        router.push('/dashboard');
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Login failed';
    }
};
</script>

<template>
  <div class="login-container">
    <div class="login-card">
      <h2 class="login-title">Login</h2>
      <form @submit.prevent="login">
        <div class="input-group">
          <input v-model="email" type="email" placeholder="Email" required class="input-field" />
        </div>
        <div class="input-group">
          <input v-model="password" type="password" placeholder="Password" required class="input-field" />
        </div>
        <button type="submit" class="login-button">Login</button>
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

.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.login-card {
  background-color: white;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
  text-align: center;
}

.login-title {
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

.login-button {
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

.login-button:hover {
  background-color: #00408d;
}

.error-message {
  color: red;
  font-size: 14px;
  margin-top: 15px;
}
</style>
