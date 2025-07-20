<template>
    <div class="wraper flex items-center justify-center h-[100vh] w-[100vw] bg-[url('/background.jpg')] bg-cover">
        <div class="login h-[18rem] backdrop-brightness-125 backdrop-blur-sm bg-purple-100/20 shadow-2xl rounded-lg p-3 divide-y divide-white flex flex-col">
            <h1 class="text-center text-3xl font-bold flex-initial text-white">
                Kira 聊天室
            </h1>
            <div class="my-3 text-center flex-auto">
                <!-- <div class = "flex flex-col justify-center items-center h-full">
                    <div class="flex flex-col md:flex-row py-3 text-purple-400">
                        <label for="username" class="w-[6rem] text-white">Username</label>
                        <input type="text" id="username" v-model="username" class="w-[12rem] bg-purple-200 text-black rounded-md p-1" />
                    </div>
                    <button type="button" id = "join" class="bg-purple-500 text-white font-bold py-2 px-4 rounded hover:bg-purple-700" @click="joinChatroom">Join</button>
                </div> -->
                <carousel-3d>
                    <slide :index="0">
                    Content 1
                    </slide>
                    <slide :index="1">
                    Content 2
                    </slide>
                </carousel-3d>
            </div>
            
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { socket } from '@/socket';
import { Carousel3d, Slide } from '@nanoandrew4/vue3-carousel-3d';

const username = ref('');

const joinChatroom = () => {
    socket.emit('join_private', { roomID: socket.id });
    if (username.value === '') {
        alert('Please enter a username');
        return;
    }
    // Set the username to the socket;
    socket.emit('join', { username: username.value });
}

socket.on('join_fail', (data) => {
    alert(data.message);
    username.value = '';
});


</script>