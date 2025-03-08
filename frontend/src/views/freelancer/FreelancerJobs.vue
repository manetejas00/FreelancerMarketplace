<template>
    <div class="container mt-4">
        <h2>Available Jobs</h2>
        <div v-if="loading" class="text-center">Loading jobs...</div>
        <div v-else-if="jobs.length === 0" class="text-center">No jobs available.</div>
        <div v-else>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Budget ($)</th>
                        <th>Category</th>
                        <th>Bid (Min)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(job, index) in jobs" :key="job.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ job.title }}</td>
                        <td>${{ job.budget }}</td>
                        <td>{{ job.category }}</td>
                        <td>{{ job.min_bid }}$ - {{ job.max_bid }}$</td>
                        <td>
                            <button v-if="appliedJobs.includes(job.id)" class="btn btn-sm btn-secondary" disabled>
                                Applied
                            </button>
                            <button v-else class="btn btn-sm btn-success" @click="openApplyModal(job)">
                                Apply
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Apply Modal -->
        <div v-if="showApplyModal" class="modal-backdrop">
            <div class="modal-content">
                <h3>Apply for {{ selectedJob.title }}</h3>
                <label for="rate">Proposed Rate:</label>
                <input v-model="rate" type="number" id="rate" required placeholder="Enter your proposed rate" />
                <form @submit.prevent="applyJob">
                    <label>Cover Letter</label>
                    <textarea v-model="coverLetter" class="form-control" required></textarea>
                    <div class="modal-actions">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-secondary" @click="closeApplyModal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { encryptData } from "@/utils/encryption";

const jobs = ref([]);
const appliedJobs = ref([]);
const loading = ref(true);
const showApplyModal = ref(false);
const selectedJob = ref({});
const coverLetter = ref("");
const bid = ref(0);
onMounted(async () => {
    await fetchJobs();
    await fetchAppliedJobs();
});

// Fetch all jobs
const fetchJobs = async () => {
    try {
        const response = await axios.get("/jobs");
        jobs.value = response.data;
    } catch (error) {
        console.error("Error fetching jobs:", error);
    } finally {
        loading.value = false;
    }
};

// Fetch applied jobs
const fetchAppliedJobs = async () => {
    try {
        const response = await axios.get("/freelancer/applied-jobs", {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        appliedJobs.value = response.data.map((app) => app.job_id);
    } catch (error) {
        console.error("Error fetching applied jobs:", error);
    }
};

// Open Apply Modal
const openApplyModal = (job) => {
    selectedJob.value = job;
    showApplyModal.value = true;
};

// Close Modal
const closeApplyModal = () => {
    showApplyModal.value = false;
};

// Apply for Job
const applyJob = async () => {
    try {
        // 🔹 Encrypt job ID and application data
        const encryptedId = encryptData(selectedJob.value.id);
        const encryptedData = encryptData(JSON.stringify({
            cover_letter: coverLetter.value,
            rate: rate.value
        }));

        await axios.post(
            `/jobs/${encryptedId}/apply`,
            { encrypted: encryptedData }, // Send encrypted data
            { headers: { Authorization: `Bearer ${localStorage.getItem("token")}` } }
        );

        alert("Application submitted!");

        // ✅ Update appliedJobs array reactively
        appliedJobs.value = [...appliedJobs.value, selectedJob.value.id];

        // ✅ Find the job in the jobs array and update its bid range
        const index = jobs.value.findIndex(job => job.id === selectedJob.value.id);
        if (index !== -1) {
            jobs.value[index] = {
                ...jobs.value[index],
                applied: true, // To disable the button
                min_bid: Math.min(jobs.value[index].min_bid, rate.value),
                max_bid: Math.max(jobs.value[index].max_bid, rate.value)
            };
        }

        // Reset fields & close modal
        coverLetter.value = "";
        rate.value = "";
        closeApplyModal();
    } catch (error) {
        console.error("Error applying for job:", error);
    }
};

</script>

<style scoped>
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 5px;
    width: 400px;
}

.modal-actions {
    margin-top: 10px;
    display: flex;
    justify-content: space-between;
}
</style>
