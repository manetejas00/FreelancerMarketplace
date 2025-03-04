<template>
    <div>
        <div>
            <h2>Login</h2>
            <form @submit.prevent="login">
                <div>
                    <input v-model="email" type="email" placeholder="Email" required />
                </div>
                <div>
                    <input v-model="password" type="password" placeholder="Password" required />
                </div>
                <button type="submit">Login</button>
                <p v-if="errorMessage">{{ errorMessage }}</p>
            </form>
        </div>
    </div>
</template>

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
