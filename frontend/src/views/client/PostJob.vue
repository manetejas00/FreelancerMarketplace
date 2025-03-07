<template>
    <div class="container mt-5">
        <div class="card shadow-sm p-4">
            <h2 class="text-center mb-4">Post a Job</h2>

            <form @submit.prevent="submitJob">
                <div class="mb-3">
                    <label class="form-label">Job Title</label>
                    <input v-model="form.title" type="text" class="form-control" required />
                    <small v-if="errors.title" class="text-danger">{{ errors.title }}</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea v-model="form.description" class="form-control" rows="4" required></textarea>
                    <small v-if="errors.description" class="text-danger">{{ errors.description }}</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Budget ($)</label>
                    <input v-model="form.budget" type="number" class="form-control" step="0.01" min="0" required />
                    <small v-if="errors.budget" class="text-danger">{{ errors.budget }}</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select v-model="form.category" class="form-control" required>
                        <option value="">Select a Category</option>
                        <option v-for="category in categories" :key="category" :value="category">
                            {{ category }}
                        </option>
                    </select>
                    <small v-if="errors.category" class="text-danger">{{ errors.category }}</small>
                </div>

                <div v-if="serverError" class="alert alert-danger">
                    {{ serverError }}
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4">Post Job</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { encryptData } from "@/utils/encryption"; // Import encryption function

const router = useRouter();
const form = ref({
    title: "",
    description: "",
    budget: "",
    category: "",
});
const errors = ref({});
const serverError = ref("");
const categories = ["Web Development", "Design", "Marketing", "Writing"];

const submitJob = async () => {
    errors.value = {};
    serverError.value = "";

    try {
        // Encrypt the form data before sending
        const encryptedData = encryptData(JSON.stringify(form.value));

        await axios.post("/jobs", { data: encryptedData }, {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });

        alert("Job posted successfully!");
        router.push("/jobs");
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            serverError.value = "An unexpected error occurred. Please try again later.";
        }
    }
};
</script>
