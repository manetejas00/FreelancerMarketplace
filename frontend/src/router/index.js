import { createRouter, createWebHistory } from "vue-router";
import Register from "../views/Register.vue";
import Login from "../views/Login.vue";
import Dashboard from "../views/home/HomeView.vue";
import JobList from "../views/jobs/JobList.vue";

const routes = [
    { path: "/", redirect: "/login" },
    { path: "/register", component: Register },
    { path: "/login", component: Login },
    { path: "/dashboard", component: Dashboard, meta: { requiresAuth: true } },
    { path: "/jobs", component: JobList },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation Guard for Authentication
router.beforeEach((to, from, next) => {
    const isAuthenticated = localStorage.getItem("token");
    if (to.meta.requiresAuth && !isAuthenticated) {
        next("/login");
    } else {
        next();
    }
});

export default router;
