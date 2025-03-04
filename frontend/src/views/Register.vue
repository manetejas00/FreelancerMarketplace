<template>
    <div>
        <div>
            <h2>Register</h2>
            <form @submit.prevent="register">
                <div>
                    <input v-model="name" placeholder="Name" required />
                </div>
                <div>
                    <input v-model="email" type="email" placeholder="Email" required />
                </div>
                <div>
                    <input v-model="password" type="password" placeholder="Password" required />
                </div>
                <div>
                    <input v-model="password_confirmation" type="password" placeholder="Confirm Password" required />
                </div>
                <div>
                    <select v-model="role">
                        <option value="freelancer">Freelancer</option>
                        <option value="client">Client</option>
                    </select>
                </div>
                <button type="submit">Register</button>
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
