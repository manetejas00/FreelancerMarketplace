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
        router.push('/dashboard');
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Login failed';
    }
};
</script>

<template>
    <div>
        <h2>Login</h2>
        <input v-model="email" placeholder="Email">
        <input v-model="password" type="password" placeholder="Password">
        <button @click="login">Login</button>
        <p v-if="errorMessage">{{ errorMessage }}</p>
    </div>
</template>
