<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="min-h-screen bg-[#050505] font-sans selection:bg-cyan-500 selection:text-black">

        <nav class="border-b border-zinc-800 bg-black/80 backdrop-blur-md sticky top-0 z-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <div class="flex shrink-0 items-center">
                            <Link :href="route('home')" class="hover:drop-shadow-[0_0_8px_#22d3ee] transition-all">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-8 h-8 bg-gradient-to-tr from-blue-600 to-cyan-400 rounded-lg flex items-center justify-center text-white font-black shadow-lg shadow-blue-500/20">
                                        C
                                    </div>
                                    <span class="text-xl font-bold tracking-tighter text-white uppercase">Cine<span
                                            class="text-blue-500">Hall</span></span>
                                </div>

                            </Link>
                        </div>

                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <NavLink :href="route('home')" :active="route().current('home')"
                                class="text-zinc-400 hover:text-cyan-400 active:text-cyan-300 font-black italic tracking-tighter uppercase transition-colors">
                                Home
                            </NavLink>
                            <NavLink :href="route('movies')" :active="route().current('movies')"
                                class="text-zinc-400 hover:text-cyan-400 active:text-cyan-300 font-black italic tracking-tighter uppercase transition-colors">
                                Movies
                            </NavLink>
                        </div>
                    </div>

                    <div class="hidden sm:ms-6 sm:flex sm:items-center">
                        <div class="relative ms-3">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button type="button"
                                            class="inline-flex items-center rounded-sm border border-zinc-800 bg-zinc-950 px-4 py-2 text-xs font-black uppercase italic tracking-widest text-cyan-500 transition duration-150 ease-in-out hover:border-cyan-500 hover:text-cyan-400 focus:outline-none">
                                            {{ $page.props.auth.user.name }}
                                            <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <div class="bg-zinc-950 border border-zinc-800 shadow-2xl">
                                        <DropdownLink :href="route('profile.edit')"
                                            class="text-zinc-400 hover:bg-cyan-900/30 hover:text-cyan-400 font-bold uppercase italic text-[10px]">
                                            PROFILE
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button"
                                            class="text-zinc-400 hover:bg-red-900/30 hover:text-red-400 font-bold uppercase italic text-[10px]">
                                            LOGOUT
                                        </DropdownLink>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <div class="-me-2 flex items-center sm:hidden">
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center rounded-md p-2 text-cyan-500 transition duration-150 ease-in-out hover:bg-zinc-900 focus:outline-none">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                                <path
                                    :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                class="sm:hidden bg-zinc-950 border-b border-zinc-800">
                <div class="space-y-1 pb-3 pt-2">
                    <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')"
                        class="text-cyan-500 font-black italic">
                        DASHBOARD
                    </ResponsiveNavLink>
                </div>
                <div class="border-t border-zinc-800 pb-1 pt-4">
                    <div class="px-4">
                        <div class="text-base font-black italic text-zinc-200 uppercase">{{ $page.props.auth.user.name
                            }}</div>
                        <div class="text-xs font-medium text-zinc-500 font-mono">{{ $page.props.auth.user.email }}</div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')" class="text-zinc-400 font-bold italic"> PROFILE
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button"
                            class="text-red-400 font-bold italic">
                            LOGOUT </ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <header class="bg-black/40 border-b border-zinc-900 shadow-xl" v-if="$slots.header">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <div class="h-8 w-1 bg-cyan-500 shadow-[0_0_10px_#22d3ee]"></div>
                    <slot name="header" />
                </div>
            </div>
        </header>

        <main class="relative">
            <div
                class="fixed inset-0 pointer-events-none opacity-[0.03] bg-[linear-gradient(rgba(18,16,16,0)_50%,rgba(0,0,0,0.25)_50%),linear-gradient(90deg,rgba(255,0,0,0.06),rgba(0,255,0,0.02),rgba(0,0,255,0.06))] z-[100] bg-[length:100%_2px,3px_100%]">
            </div>

            <slot />
        </main>
    </div>
</template>

<style>
html,
body {
    overscroll-behavior: none;
}

/* Global Persona Fonts & Scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #050505;
}

::-webkit-scrollbar-thumb {
    background: #1f2937;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #22d3ee;
}
</style>