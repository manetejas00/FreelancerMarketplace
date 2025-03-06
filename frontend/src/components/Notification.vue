<script setup>
import { onMounted } from "vue";
import Echo from "laravel-echo";
import Pusher from "pusher-js";

onMounted(() => {
    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: "pusher",
        key: "your-app-key",
        cluster: "mt1",
        forceTLS: true,
    });

    window.Echo.channel("notifications")
        .listen(".new-notification", (e) => {
            alert("New Notification: " + e.message);
        });
});
</script>
