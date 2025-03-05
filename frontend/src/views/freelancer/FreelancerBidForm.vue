<template>
    <form @submit.prevent="submitBid">
      <label for="rate">Proposed Rate:</label>
      <input
        v-model="rate"
        type="number"
        id="rate"
        required
        placeholder="Enter your proposed rate"
      />

      <label for="coverLetter">Cover Letter:</label>
      <textarea
        v-model="coverLetter"
        id="coverLetter"
        required
        placeholder="Enter your cover letter"
      ></textarea>

      <button type="submit" :disabled="loading">Submit Bid</button>
    </form>
  </template>

  <script setup>
  import { ref } from 'vue';
  import axios from 'axios';

  const rate = ref('');
  const coverLetter = ref('');
  const loading = ref(false);

  const submitBid = async () => {
    loading.value = true;
    try {
      const response = await axios.post(`/api/jobs/${jobId}/place-bid`, {
        rate: rate.value,
        cover_letter: coverLetter.value,
      });
      alert(response.data.message);
    } catch (error) {
      alert('There was an error submitting your bid');
    } finally {
      loading.value = false;
    }
  };
  </script>
