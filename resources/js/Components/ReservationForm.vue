<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    session: Object
});

const emit = defineEmits(['close']);

const seats = ref([]);
const selectedSeats = ref([]);
const loading = ref(true);

const fetchSeats = async () => {
    try {
        loading.value = true;
        // Fetching seats specifically for THIS session
        const response = await axios.get(`/api/v1/rooms/${props.session.room_id}/seats`);
        seats.value = response.data;

    } catch (error) {
        console.error("FAILED_TO_SYNC_SEAT_MAP:", error);
    } finally {
        loading.value = false;
    }
};

const toggleSeat = (seatId) => {
    if (selectedSeats.value.includes(seatId)) {
        selectedSeats.value = selectedSeats.value.filter(id => id !== seatId);
    } else {
        selectedSeats.value.push(seatId);
    }
};

onMounted(() => {
    fetchSeats();
});
</script>

<template>
    <div
        class="w-full max-w-4xl bg-zinc-950 border border-zinc-800 p-8 rounded-lg shadow-[0_0_50px_rgba(0,0,0,1)] relative overflow-hidden">

        <div class="flex justify-between items-end mb-12 border-b border-zinc-900 pb-6">
            <div>
                <h3 class="text-3xl font-black italic uppercase tracking-tighter text-white">
                    Select Your Seats
                </h3>
                <p class="text-[20px] font-mono text-cyan-500 mt-2">
                    Ticket Price: {{ session.price }} MAD
                </p>
            </div>
            <div class="text-right">
                <span class="block text-[10px] text-zinc-500 uppercase font-black">TOTAL DUE</span>
                <span class="text-2xl font-mono text-cyan-400">{{ (selectedSeats.length * session.price).toFixed(2) }}
                    MAD</span>
            </div>
        </div>

        <div
            class="w-full h-2 bg-gradient-to-r from-transparent via-cyan-900 to-transparent mb-16 rounded-full shadow-[0_-10px_20px_rgba(34,211,238,0.2)]">
        </div>

        <div v-if="!loading" class="grid grid-cols-8 gap-3 justify-center mb-12">
            <div v-for="seat in seats" :key="seat.id" @click="!seat.is_booked && toggleSeat(seat.id)" :class="[
                'aspect-square rounded-sm border transition-all cursor-pointer flex items-center justify-center text-[8px] font-mono',
                seat.is_booked ? 'bg-zinc-900 border-zinc-800 text-zinc-700 cursor-not-allowed' :
                    selectedSeats.includes(seat.id) ? 'bg-cyan-500 border-cyan-400 text-black shadow-[0_0_15px_rgba(34,211,238,0.5)]' :
                        'bg-black border-zinc-700 text-zinc-500 hover:border-cyan-500'
            ]">
                {{ seat.label }}
            </div>
        </div>

        <div v-else class="py-20 flex flex-col items-center">
            <div class="w-8 h-8 border-2 border-cyan-500 border-t-transparent animate-spin rounded-full"></div>
            <span class="text-[10px] font-mono text-cyan-800 mt-4 animate-pulse uppercase">READING_SEAT_STATUS...</span>
        </div>

        <div class="flex gap-4">
            <button @click="$emit('close')"
                class="flex-1 py-4 bg-zinc-900 text-zinc-500 font-black uppercase italic hover:bg-zinc-800 transition">
                CANCEL ORDER
            </button>
            <button :disabled="selectedSeats.length === 0"                       
                class="flex-2 px-12 py-4 bg-cyan-600 text-white font-black uppercase italic hover:bg-cyan-500 disabled:opacity-20 disabled:cursor-not-allowed transition shadow-[0_0_20px_rgba(34,211,238,0.2)]">
                CONFIRM RESERVATION
            </button>
        </div>
    </div>
</template>