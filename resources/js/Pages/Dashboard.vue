<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head} from "@inertiajs/vue3";
import { ref } from 'vue';

const n1 = ref(0);
const n2 = ref(0);

// setTimeout(() => {
//     isPressed.value = true;    
// }, 3000);

</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">You're logged in!</div>
          <input type="text" name="sum" v-model.number="n1">
          <input type="text" name="sum" v-model.number="n2">
          <h1>{{(n1 + n2) || 0}}</h1>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
