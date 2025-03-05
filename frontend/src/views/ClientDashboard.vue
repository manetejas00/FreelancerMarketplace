<script setup>
import { ref, watchEffect } from "vue";
import JobList from "@/views/client/JobList.vue"; // Adjust path based on your project structure

const user = ref({
    name: localStorage.getItem("name") || "Guest", // Fetch name from localStorage
    role: localStorage.getItem("role") || "Guest" // Fetch role from localStorage
});
watchEffect(() => {
    user.value.name = localStorage.getItem("name") || "Guest";
    user.value.role = localStorage.getItem("role") || "Guest";
});
</script>

<template>
    <div>
        <h2>Welcome, {{ user.name }}!</h2>
        <JobList />

    </div>
</template>
