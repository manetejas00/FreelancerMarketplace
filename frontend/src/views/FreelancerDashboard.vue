<script setup>
import { ref } from "vue";
import FreelancerJobs from "@/views/freelancer/FreelancerJobs.vue"; // Adjust path based on your project structure

const user = ref({
    name: localStorage.getItem("name") || "Guest", // Fetch name from localStorage
    role: localStorage.getItem("role") || "Guest" // Fetch role from localStorage
});
</script>

<template>
    <div>
        <h2>Welcome, {{ user.name }}!</h2>
        <FreelancerJobs />
    </div>
</template>
