<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ReservationForm from '@/Components/ReservationForm.vue';
import { Head } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import axios from "axios";

const films = ref([]);
const selectedFilm = ref(null);
const loading = ref(true);
const activeSession = ref(null);

const fetchFilms = async () => {
    try {
        loading.value = true;
        const response = await axios.get("/api/v1/films");
        films.value = response.data.data;
        if (films.value.length > 0) {
            selectedFilm.value = films.value[0];
        }
    } catch (error) {
        console.error("API Error:", error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchFilms();
});
</script>

<template>

    <Head title="Browse Movies" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-[#050505] text-white">

            <div class="pt-8 pb-4 px-4 sm:px-6 lg:px-8">
                <div class="max-w-7xl mx-auto">
                    <div class="flex overflow-x-auto pb-6 gap-6 scrollbar-hide">
                        <div v-for="film in films" :key="film.id" @click="selectedFilm = film"
                            class="min-w-[180px] group cursor-pointer">
                            <div :class="selectedFilm?.id === film.id ? 'border-cyan-400 scale-105 shadow-[0_0_20px_rgba(34,211,238,0.3)]' : 'border-zinc-800 scale-100 opacity-60 hover:opacity-100'"
                                class="relative aspect-[2/3] overflow-hidden rounded-sm border-2 transition-all duration-500 bg-zinc-900">
                                <img :src="film.image ? `/storage/${film.image.path}` : 'https://placehold.co/400x600/111/fff?text=NO_SIGNAL'"
                                    class="h-full w-full object-cover grayscale-[0.5] group-hover:grayscale-0 transition-all" />
                            </div>
                            <p class="mt-3 text-[11px] font-bold tracking-tight uppercase truncate">{{ film.title }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 sm:px-6 lg:px-8 pb-12">
                <div class="max-w-7xl mx-auto">
                    <transition name="slide-up" mode="out-in">
                        <div v-if="selectedFilm && !loading" :key="selectedFilm.id"
                            class="relative group overflow-hidden bg-zinc-950 border border-zinc-800 rounded-lg shadow-2xl flex flex-col lg:flex-row">
                            <div class="lg:w-[400px] h-[400px] lg:h-auto overflow-hidden">
                                <img :src="selectedFilm.image ? `/storage/${selectedFilm.image.path}` : 'https://placehold.co/600x900/111/333?text=FILM_DATA'"
                                    class="h-full w-full object-cover scale-105 group-hover:scale-100 transition-transform duration-1000" />
                            </div>

                            <div class="p-8 lg:p-12 flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                                        <h2
                                            class="text-5xl font-black leading-none italic tracking-tighter uppercase drop-shadow-[2px_2px_0px_#22d3ee]">
                                            {{ selectedFilm.title }}
                                        </h2>
                                        <div
                                            class="flex items-center gap-4 bg-zinc-900 px-4 py-2 rounded-sm border-l-4 border-cyan-500">
                                            <span class="text-cyan-400 font-mono font-bold">{{ selectedFilm.rate
                                                }}</span>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2 mt-6">
                                        <span v-for="genre in selectedFilm.genres" :key="genre.id"
                                            class="text-[10px] bg-cyan-950 text-cyan-400 border border-cyan-900 px-3 py-1 rounded-full uppercase font-black">
                                            {{ genre.name }}
                                        </span>
                                        <span
                                            class="text-[10px] text-zinc-500 py-1 font-mono uppercase tracking-widest">
                                            Runtime: {{ selectedFilm.duration }}H
                                        </span>
                                    </div>

                                    <p class="mt-8 text-zinc-400 leading-relaxed max-w-2xl text-lg font-light italic">
                                        "{{ selectedFilm.description }}"
                                    </p>
                                </div>

                                <div class="mt-12">
                                    <h4 class="text-[20px] font-black text-white-600 uppercase tracking-[0.5em] mb-6">
                                        Sessions</h4>

                                    <div v-if="selectedFilm.sessions && selectedFilm.sessions.length > 0"
                                        class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                        <button v-for="session in selectedFilm.sessions" :key="session.id"
                                            @click="activeSession = session"
                                            class="group relative py-4 border border-zinc-800 bg-black overflow-hidden hover:border-cyan-500 transition-colors text-left px-4">

                                            <div
                                                class="absolute inset-0 bg-cyan-500/10 translate-y-full group-hover:translate-y-0 transition-transform">
                                            </div>

                                            <div class="flex gap-10">
                                                <div class="relative flex flex-col">
                                                    <span
                                                        class="font-mono font-bold text-lg text-zinc-400 group-hover:text-cyan-400">
                                                        {{ session.start_time?.split(' ')[0]?.slice(5, 10) }}
                                                    </span>
                                                    <span
                                                        class="font-mono font-bold text-lg text-zinc-400 group-hover:text-cyan-400">
                                                        {{ session.start_time?.split(' ')[1]?.slice(0, 5) }}
                                                    </span>
                                                </div>
                                                <span
                                                    class="font-mono font-bold text-xl uppercase italic tracking-tighter transition-colors"
                                                    :class="{
                                                        'text-emerald-500 group-hover:text-emerald-400': session.room.type === 'Normal',
                                                        'text-yellow-400 group-hover:text-yellow-300 drop-shadow-[0_0_8px_rgba(250,204,21,0.4)]': session.room.type === 'VIP',
                                                        'text-zinc-400': !['normal', 'vip'].includes(session.room.type)
                                                    }">
                                                    {{ session.room.type }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between items-center mt-1">
                                                <span
                                                    class="text-[9px] text-zinc-600 uppercase font-black group-hover:text-cyan-600">
                                                    {{ session.language }}
                                                </span>
                                                <span
                                                    class="text-[10px] text-cyan-500/50 font-mono group-hover:text-cyan-400">
                                                    {{ session.price }} MAD
                                                </span>
                                            </div>

                                        </button>
                                    </div>

                                    <div v-else
                                        class="text-zinc-700 italic text-sm border border-dashed border-zinc-800 p-4 rounded">
                                        NO_SESSIONS_FOUND_FOR_UNIT_ID_{{ selectedFilm.id }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                    <transition name="fade-blur">
                        <div v-if="activeSession"
                            class="fixed inset-0 z-[100] bg-black/90 backdrop-blur-xl flex items-center justify-center p-4">

                            <ReservationForm :session="activeSession" @close="activeSession = null" />

                            <button @click="activeSession = null"
                                class="absolute top-8 right-8 text-zinc-500 hover:text-cyan-400 font-mono text-xs uppercase tracking-[0.3em]">
                                [ ESC_TO_EXIT ]
                            </button>
                        </div>
                    </transition>

                    <div v-if="loading" class="flex flex-col items-center justify-center py-32 space-y-4">
                        <div class="w-12 h-12 border-4 border-cyan-500 border-t-transparent rounded-full animate-spin">
                        </div>
                        <span
                            class="text-xs font-mono text-cyan-500 animate-pulse tracking-[0.4em]">SYNCING_SERVER...</span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-up-enter-from {
    opacity: 0;
    transform: translateY(30px);
}

.slide-up-leave-to {
    opacity: 0;
    transform: translateY(-30px);
}
</style>