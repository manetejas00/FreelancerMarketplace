<template>
    <div class="container mt-5">
        <div class="card shadow-sm p-4">
            <h2 class="text-center mb-4">Freelancer Profile</h2>

            <form @submit.prevent="updateProfile">
                <!-- Full Name -->
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input v-model="form.name" type="text" class="form-control" required />
                    <small v-if="errors.name" class="text-danger">{{ errors.name }}</small>
                </div>

                <!-- Skills -->
                <div class="mb-3">
                    <label class="form-label">Skills (comma-separated)</label>
                    <input v-model="form.skills" type="text" class="form-control" required />
                    <small v-if="errors.skills" class="text-danger">{{ errors.skills }}</small>
                </div>

                <!-- Experience -->
                <div class="mb-3">
                    <label class="form-label">Experience (Years)</label>
                    <input v-model="form.experience" type="number" class="form-control" required />
                    <small v-if="errors.experience" class="text-danger">{{ errors.experience }}</small>
                </div>

                <!-- Company Name -->
                <div class="mb-3">
                    <label class="form-label">Company Name</label>
                    <input v-model="form.company_name" type="text" class="form-control" />
                    <small v-if="errors.company_name" class="text-danger">{{ errors.company_name }}</small>
                </div>

                <!-- Project Details -->
                <div class="mb-3">
                    <label class="form-label">Project Details</label>
                    <textarea v-model="form.project_details" class="form-control"></textarea>
                    <small v-if="errors.project_details" class="text-danger">{{ errors.project_details }}</small>
                </div>

                <!-- Working Developers Count -->
                <div class="mb-3">
                    <label class="form-label">Number of Developers in Project</label>
                    <input v-model="form.working_developers_count" type="number" class="form-control" min="0" />
                    <small v-if="errors.working_developers_count" class="text-danger">{{ errors.working_developers_count }}</small>
                </div>

                <!-- Portfolio Link -->
                <div class="mb-3">
                    <label class="form-label">Portfolio URL</label>
                    <input v-model="form.portfolio" type="url" class="form-control" />
                    <small v-if="errors.portfolio" class="text-danger">{{ errors.portfolio }}</small>
                </div>

                <!-- Hourly Rate -->
                <div class="mb-3">
                    <label class="form-label">Hourly Rate ($)</label>
                    <input v-model="form.hourly_rate" type="number" class="form-control" required />
                    <small v-if="errors.hourly_rate" class="text-danger">{{ errors.hourly_rate }}</small>
                </div>

                <!-- Display Server Errors -->
                <div v-if="serverError" class="alert alert-danger">
                    {{ serverError }}
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();
const form = ref({
    name: "",
    skills: "",
    experience: "",
    company_name: "",
    project_details: "",
    working_developers_count: "",
    portfolio: "",
    hourly_rate: "",
});

const errors = ref({}); // Store validation errors
const serverError = ref(""); // Store general server error

const fetchProfile = async () => {
    try {
        const response = await axios.get("/freelancer/profile", {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        form.value = response.data;

        // Update localStorage
        localStorage.setItem("name", response.data.name);
        localStorage.setItem("role", response.data.role); // Assuming role exists
    } catch (error) {
        console.error("Error fetching profile:", error);
        serverError.value = "Failed to load profile. Please try again later.";
    }
};

const updateProfile = async () => {
    errors.value = {}; // Reset errors
    serverError.value = ""; // Reset server error

    try {
        const response = await axios.post("/freelancer/profile", form.value, {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });

        // Update localStorage
        localStorage.setItem("name", response.data.name);
        localStorage.setItem("role", response.data.role);

        alert("Profile updated successfully!");
        router.push("/freelancer/dashboard");
    } catch (error) {
        if (error.response && error.response.status === 422) {
            // Validation errors
            errors.value = error.response.data.errors;
        } else {
            // General errors
            serverError.value = "An unexpected error occurred. Please try again later.";
        }
        console.error("Error updating profile:", error);
    }
};

onMounted(fetchProfile);
</script>
