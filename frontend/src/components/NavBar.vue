<script setup>
import { ref, watchEffect } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const token = ref(localStorage.getItem("token"));
const role = ref(localStorage.getItem("role"));
const user = ref({
    name: localStorage.getItem("name") || "Guest", // Fetch name from localStorage
    role: localStorage.getItem("role") || "Guest" // Fetch role from localStorage
});
const logout = () => {
    localStorage.removeItem("token");
    localStorage.removeItem("role");
    token.value = null;
    role.value = null;
    router.push("/login");
};

// Listen for localStorage changes dynamically
window.addEventListener("storage", () => {
    token.value = localStorage.getItem("token");
    role.value = localStorage.getItem("role");
});

// Update state when login/logout happens
watchEffect(() => {
    token.value = localStorage.getItem("token");
    role.value = localStorage.getItem("role");
});
</script>

<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <!-- Brand Logo -->
            <router-link to="/" class="navbar-brand">FreelanceHub - {{ user.role }}</router-link>
            <!-- Toggle Button for Mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li v-if="token && role === 'freelancer'" class="nav-item">
                        <router-link to="/" class="nav-link">Freelancer Apply Jobs</router-link>
                    </li>
                    <li v-if="role === 'client'" class="nav-item">
                        <router-link to="/jobs" class="nav-link">Jobs List</router-link>
                    </li>
                    <li v-if="role === 'client'" class="nav-item">
                        <router-link to="/client/bids-list" class="nav-link">Bids List</router-link>
                    </li>
                    <li v-if="role === 'client'" class="nav-item">
                        <router-link to="/client/profile-list" class="nav-link">Profile List</router-link>
                    </li>
                    <li v-if="role === 'client'" class="nav-item">
                        <router-link to="/post-job" class="nav-link">PostJob List</router-link>
                    </li>
                    <li v-if="!token" class="nav-item">
                        <router-link to="/login" class="nav-link">Login</router-link>
                    </li>
                    <li v-if="!token" class="nav-item">
                        <router-link to="/register" class="nav-link">Register</router-link>
                    </li>
                    <li v-if="token && role === 'freelancer'" class="nav-item">
                        <router-link class="nav-link" to="/freelancer/profile">Freelancer Profile</router-link>
                    </li>
                    <li v-if="token && role === 'freelancer'" class="nav-item">
                        <router-link class="nav-link" to="/freelancer/bid/form">Freelancer bid form</router-link>
                    </li>
                    <li v-if="token" class="nav-item">
                        <button class="btn btn-danger ms-2" @click="logout">Logout</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>
