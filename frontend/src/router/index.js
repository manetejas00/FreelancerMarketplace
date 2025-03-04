import { createRouter, createWebHistory } from "vue-router";
import Register from "../views/Register.vue";
import Login from "../views/Login.vue";
import Dashboard from "../views/home/HomeView.vue";
import JobList from "../views/jobs/JobList.vue";
import JobManagement from "../views/jobs/JobManagement.vue";

const routes = [
    { path: "/", redirect: "/login" },
    { path: "/register", component: Register },
    { path: "/login", component: Login },
    {
        path: "/dashboard",
        component: Dashboard,
        meta: { requiresAuth: true, roles: ["client", "admin","freelancer"] },
    },
    {
        path: "/jobs",
        component: JobList,
        meta: { requiresAuth: true, roles: ["freelancer"] },
    },
    {
        path: "/manage-jobs",
        component: JobManagement,
        meta: { requiresAuth: true, roles: ["client"] },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = localStorage.getItem("token");
    const userRole = localStorage.getItem("roles");
    if (to.meta.requiresAuth && !isAuthenticated) {
        return next("/login");
    }
    if (to.meta.roles && to.meta.roles.length > 0) {
        if (!userRole || !to.meta.roles.includes(userRole)) {
            return next("/dashboard");
        }
    }
    next();
});

export default router;
