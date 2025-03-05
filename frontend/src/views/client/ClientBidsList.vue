<template>
    <v-list>
        <v-list-item v-for="bid in bids" :key="bid.id">
            <v-list-item-content>
                <v-list-item-title>{{ bid.user.name }}</v-list-item-title>
                <v-list-item-subtitle>{{ bid.cover_letter }}</v-list-item-subtitle>
                <v-list-item-action>
                    <v-btn @click="updateBidStatus(bid.id, 'accepted')" color="green">Accept</v-btn>
                    <v-btn @click="updateBidStatus(bid.id, 'rejected')" color="red">Reject</v-btn>
                </v-list-item-action>
            </v-list-item-content>
        </v-list-item>
    </v-list>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const bids = ref([]);

const fetchBids = async () => {
    const response = await axios.get(`/api/jobs/${jobId}/bids`);
    bids.value = response.data;
};

const updateBidStatus = async (bidId, status) => {
    try {
        const response = await axios.post(`/api/bids/${bidId}/manage`, { status });
        alert(response.data.message);
        fetchBids(); // Refresh the bid list
    } catch (error) {
        alert('Error updating bid status');
    }
};

fetchBids();
</script>
