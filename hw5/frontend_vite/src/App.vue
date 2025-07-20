<script setup>
import chatroom from '@components/chatroom.vue';
import login from '@components/login.vue';
import { ref } from 'vue';
import { socket } from '@/socket';


const currentComponent = ref('login');

socket.on('join_success', (data) => {
    currentComponent.value = 'chatroom';
});
</script>

<template>
    <login v-if="currentComponent === 'login'" />
    <chatroom v-else />
</template>

