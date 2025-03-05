<script setup>
import { ref, watchEffect } from "vue";
import FreelancerJobs from "@/views/freelancer/FreelancerJobs.vue"; // Adjust path based on your project structure

const user = ref({
    name: localStorage.getItem("name") || "Guest",
    role: localStorage.getItem("role") || "Guest"
});

// Watch for changes in localStorage and update `user`
watchEffect(() => {
    user.value.name = localStorage.getItem("name") || "Guest";
    user.value.role = localStorage.getItem("role") || "Guest";
});
</script>

<template>
    <div>
        <h2>Welcome, {{ user.name }}!</h2>
        <FreelancerJobs />
    </div>
</template>
