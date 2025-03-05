<script setup>
import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const name = ref("");
const email = ref("");
const password = ref("");
const role = ref("client");
const router = useRouter();
const message = ref("");

const register = async () => {
    try {
        await axios.post("/auth/register", { name: name.value, email: email.value, password: password.value, role: role.value });
        router.push("/login");
    } catch (error) {
        message.value = "Registration failed!";
    }
};
</script>

<template>
    <div>
        <h2>Register</h2>
        <p v-if="message" style="color: red;">{{ message }}</p>
        <input v-model="name" placeholder="Name" />
        <input v-model="email" placeholder="Email" type="email" />
        <input v-model="password" placeholder="Password" type="password" />
        <select v-model="role">
            <option value="client">Client</option>
            <option value="freelancer">Freelancer</option>
        </select>
        <button @click="register">Register</button>
    </div>
</template>
