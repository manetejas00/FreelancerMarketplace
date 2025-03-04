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
        router.push('/dashboard');
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Registration failed';
    }
};
</script>

<template>
    <div>
        <h2>Register</h2>
        <input v-model="name" placeholder="Name">
        <input v-model="email" placeholder="Email">
        <input v-model="password" type="password" placeholder="Password">
        <input v-model="password_confirmation" type="password" placeholder="Confirm Password">
        <select v-model="role">
            <option value="freelancer">Freelancer</option>
            <option value="client">Client</option>
        </select>
        <button @click="register">Register</button>
        <p v-if="errorMessage">{{ errorMessage }}</p>
    </div>
</template>
