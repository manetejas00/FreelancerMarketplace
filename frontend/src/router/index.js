import { createRouter, createWebHistory } from "vue-router";
import Login from "../views/Login.vue";
import Register from "../views/Register.vue";
import ClientDashboard from "../views/ClientDashboard.vue";
import FreelancerDashboard from "../views/FreelancerDashboard.vue";
import Profile from "@/views/freelancer/Profile.vue";
import ProfileList from "@/views/client/ProfileList.vue";
import PostJob from "@/views/client/PostJob.vue";
import JobList from "@/views/client/JobList.vue";
import FreelancerJobs from "@/views/freelancer/FreelancerJobs.vue";

const routes = [
    { path: "/", redirect: "/login" },
    { path: "/login", component: Login, meta: { guest: true } },
    { path: "/register", component: Register, meta: { guest: true } },
    {
        path: "/client/dashboard",
        component: ClientDashboard,
        meta: { requiresAuth: true, role: "client" },
    },
    {
        path: "/freelancer/dashboard",
        component: FreelancerDashboard,
        meta: { requiresAuth: true, role: "freelancer" },
    },
    {
        path: "/freelancer/profile",
        component: Profile,
        meta: { requiresAuth: true, role: "freelancer" },
    },
    {
        path: "/freelancer/jobs",
        component: FreelancerJobs,
        meta: { requiresAuth: true, role: "freelancer" },
    },
    {
        path: "/client/profile-list",
        component: ProfileList,
        meta: { requiresAuth: true, role: "client" },
    },
    {
        path: "/jobs",
        name: "JobList",
        component: JobList,
        meta: { requiresAuth: true, role: "client" },
    },
    {
        path: "/post-job",
        name: "PostJob",
        component: PostJob,
        meta: { requiresAuth: true, role: "client" },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Global Navigation Guard
router.beforeEach((to, from, next) => {
    const isAuthenticated = !!localStorage.getItem("token"); // Check if user is logged in
    const userRole = localStorage.getItem("role"); // Get user's role (client or freelancer)

    if (to.meta.requiresAuth && !isAuthenticated) {
        // If the route requires authentication but the user is not logged in, redirect to login
        next("/login");
    } else if (to.meta.guest && isAuthenticated) {
        // If the route is for guests (login/register) but the user is already logged in, redirect based on role
        if (userRole === "client") {
            next("/client/dashboard");
        } else {
            next("/freelancer/dashboard");
        }
    } else if (
        to.meta.requiresAuth &&
        to.meta.role &&
        to.meta.role !== userRole
    ) {
        // If user is authenticated but trying to access a page that does not match their role
        if (userRole === "client") {
            next("/client/dashboard");
        } else {
            next("/freelancer/dashboard");
        }
    } else {
        // Proceed normally
        next();
    }
});

export default router;
