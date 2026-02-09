<template>
  <div class="sticky top-0 z-40 bg-white shadow-md">
    <div class="container mx-auto px-4 py-3">
      <div class="flex items-center justify-between flex-wrap gap-2">
        <div class="flex items-center space-x-2 md:space-x-4">
          <h1 class="text-xl md:text-2xl font-bold text-gray-800">Work Tracker</h1>
        </div>

        <div class="flex items-center space-x-2 md:space-x-3 flex-wrap">
          <a
            href="https://github.com"
            target="_blank"
            rel="noopener noreferrer"
            class="px-3 py-1.5 text-sm bg-gray-800 text-white rounded hover:bg-gray-700 transition-colors"
          >
            GitHub
          </a>
          <a
            href="#"
            class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
          >
            Server
          </a>
          <a
            href="#"
            class="px-3 py-1.5 text-sm bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
          >
            Invoicing
          </a>
          <a
            href="https://wa.me/"
            target="_blank"
            rel="noopener noreferrer"
            class="px-3 py-1.5 text-sm bg-green-500 text-white rounded hover:bg-green-600 transition-colors"
          >
            WhatsApp
          </a>

          <template v-if="isAuthenticated">
            <router-link
              to="/admin"
              class="px-3 py-1.5 text-sm bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors"
            >
              Admin
            </router-link>
            <button
              @click="handleLogout"
              class="px-3 py-1.5 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition-colors"
            >
              Logout
            </button>
          </template>
          <template v-else>
            <button
              @click="emit('openLogin')"
              class="px-3 py-1.5 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors"
            >
              Login
            </button>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineEmits } from 'vue';
import { useAuth } from '../composables/useAuth';

const emit = defineEmits(['openLogin']);
const { isAuthenticated, logout } = useAuth();

const handleLogout = async () => {
  await logout();
  window.location.href = '/';
};
</script>
