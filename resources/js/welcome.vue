<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Welcome to CineHall" />

    <div class="min-h-screen bg-[#020617] text-[#94a3b8] font-sans selection:bg-[#38bdf8] selection:text-white relative overflow-hidden">
        
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-blue-900/20 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[0%] right-[0%] w-[30%] h-[30%] bg-indigo-900/10 blur-[100px] rounded-full"></div>

        <nav class="relative z-10 max-w-7xl mx-auto px-6 py-8 flex justify-between items-center border-b border-white/5">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-tr from-blue-600 to-cyan-400 rounded-lg flex items-center justify-center text-white font-black shadow-lg shadow-blue-500/20">
                    C
                </div>
                <span class="text-xl font-bold tracking-tighter text-white uppercase">Cine<span class="text-blue-500">Hall</span></span>
            </div>
            
            <div class="flex items-center gap-6 text-sm font-medium uppercase tracking-widest">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-white hover:text-blue-400 transition-colors">Terminal</Link>
                <template v-else>
                    <Link :href="route('login')" class="hover:text-white transition-colors">Sign In</Link>
                    <Link v-if="canRegister" :href="route('register')" class="px-4 py-2 bg-white/5 border border-white/10 rounded-md hover:bg-white/10 hover:border-white/20 transition-all text-white">Join</Link>
                </template>
            </div>
        </nav>

        <main class="relative z-10 max-w-7xl mx-auto px-6 pt-20 pb-32 grid lg:grid-cols-2 gap-16 items-center">
            
            <section>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-widest mb-6">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                    </span>
                    Now Streaming in Sector_07
                </div>

                <h1 class="text-6xl md:text-8xl font-black text-white leading-none mb-6 tracking-tighter">
                    CINEMA <br/> REIMAGINED.
                </h1>

                <p class="text-lg text-slate-400 max-w-md leading-relaxed mb-10 italic">
                    Experience the melancholy of the silver screen. Precision booking, VIP access, and an atmosphere built for the night.
                </p>

                <div class="flex flex-wrap gap-4">
                    <Link :href="route('login')" class="group relative px-8 py-4 bg-blue-600 text-white font-bold rounded-xl overflow-hidden transition-all hover:bg-blue-500 shadow-xl shadow-blue-900/20">
                        <span class="relative z-10 flex items-center gap-2">
                            BOOK TICKETS
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                        </span>
                    </Link>
                    <button class="px-8 py-4 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-all uppercase tracking-widest text-sm">
                        View Catalog
                    </button>
                </div>
            </section>

            <section class="hidden lg:block relative">
                <div class="relative rounded-2xl overflow-hidden border border-white/10 shadow-2xl rotate-2 hover:rotate-0 transition-transform duration-700 aspect-[4/5] bg-[#0f172a]">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#020617] via-transparent to-transparent z-10"></div>
                    <div class="absolute bottom-8 left-8 z-20">
                        <p class="text-blue-400 font-bold text-xs uppercase tracking-[0.3em] mb-2">Featured Tonight</p>
                        <h2 class="text-3xl font-black text-white uppercase italic">Burn My Dread</h2>
                    </div>
                </div>
                <div class="absolute -bottom-6 -left-6 p-6 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl z-30 max-w-[200px]">
                    <p class="text-[10px] uppercase tracking-widest text-slate-500 mb-1">Status</p>
                    <p class="text-sm font-bold text-green-400 uppercase tracking-tighter">95% Capacity</p>
                </div>
            </section>
        </main>

        <footer class="relative z-10 border-t border-white/5 py-10">
            <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6 text-[10px] uppercase tracking-[0.4em] opacity-40">
                <p>&copy; 2026 CineHall Operations</p>
                <div class="flex gap-8">
                    <span>Safi_Main_Branch</span>
                    <span>System_Live</span>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Scoped styles for that extra touch */
h1 {
    text-shadow: 0 10px 30px rgba(0,0,0,0.5);
}
</style>