<script setup>
import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const email = ref("");
const password = ref("");
const router = useRouter();
const errorMessage = ref("");

const login = async () => {
    try {
        const response = await axios.post("/login", { email: email.value, password: password.value });

        localStorage.setItem("token", response.data.token);
        localStorage.setItem("name", response.data.user.name);
        localStorage.setItem("role", response.data.user.role);

        if (response.data.user.role === "client") {
            router.push("/client/dashboard");
        } else {
            router.push("/freelancer/dashboard");
        }
    } catch (error) {
        errorMessage.value = "Invalid email or password";
    }
};
</script>

<template>
    <div>
        <h2>Login</h2>
        <p v-if="errorMessage" style="color: red;">{{ errorMessage }}</p>
        <input v-model="email" placeholder="Email" type="email" />
        <input v-model="password" placeholder="Password" type="password" />
        <button @click="login">Login</button>
    </div>
</template>
