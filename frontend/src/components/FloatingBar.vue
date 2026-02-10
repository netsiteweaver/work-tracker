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
            class="flex items-center gap-1.5 px-3 py-1.5 text-sm border-2 border-gray-800 text-gray-800 rounded hover:bg-gray-800 hover:text-white transition-colors"
          >
            <img src="../assets/github-64px.png" alt="GitHub" class="w-4 h-4" />
            GitHub
          </a>
          <div ref="serverDropdownRef" class="relative">
            <button
              @click.stop="toggleServerDropdown"
              class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-green-400 text-white rounded hover:bg-green-500 transition-colors"
            >
              <img src="../assets/servers-64px.png" alt="Server" class="w-4 h-4" />
              Server
              <ChevronDownIcon class="w-4 h-4" />
            </button>
            <div
              v-if="showServerDropdown"
              class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50"
            >
              <!-- Notch/Arrow pointing up -->
              <div class="absolute -top-2 right-4 w-4 h-4 bg-white border-l border-t border-gray-200 transform rotate-45"></div>
              <div class="relative bg-white rounded-md">
                <a
                  v-for="server in servers"
                  :key="server.id"
                  :href="server.url"
                  target="_blank"
                  rel="noopener noreferrer"
                  @click="closeServerDropdown"
                  class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors first:rounded-t-md last:rounded-b-md"
                >
                  <img :src="server.icon" :alt="server.name" class="w-4 h-4" />
                  {{ server.name }}
                </a>
              </div>
            </div>
          </div>
          <a
            href="https://hms.netsiteweaver.com"
            class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
          >
            <img src="../assets/hms-64px.png" alt="HMS" class="w-4 h-4" />
            HMS
          </a>
          <a
            href="https://web.whatsapp.com/"
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-green-500 text-white rounded hover:bg-green-600 transition-colors"
          >
            <img src="../assets/whatsapp-64px.png" alt="WhatsApp" class="w-4 h-4" />
            WhatsApp
          </a>
          <a
            href="https://gitlab.com"
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-orange-600 text-white rounded hover:bg-orange-700 transition-colors"
          >
            <img src="../assets/gitlab-64px.png" alt="GitLab" class="w-4 h-4" />
            GitLab
          </a>
          <a
            href="http://localhost/phpmyadmin"
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-teal-600 text-white rounded hover:bg-teal-700 transition-colors"
          >
            <img src="../assets/phpmyadmin-64px.png" alt="phpMyAdmin" class="w-4 h-4" />
            phpMyAdmin
          </a>
          <a
            href="https://app.tweezzo.online/users/signin"
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-cyan-600 text-white rounded hover:bg-cyan-700 transition-colors"
          >
            <img src="../assets/tweezzo-64px.png" alt="Tweezzo" class="w-4 h-4" />
            Tweezzo
          </a>

          <template v-if="isAuthenticated">
            <router-link
              :to="adminToggleTarget"
              class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors"
            >
              <ArrowLeftIcon v-if="isAdminRoute" class="w-4 h-4" />
              <ArrowRightIcon v-else class="w-4 h-4" />
              {{ adminToggleLabel }}
            </router-link>
            <button
              @click="handleLogout"
              class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition-colors"
            >
              <ArrowRightOnRectangleIcon class="w-4 h-4" />
              Logout
            </button>
          </template>
          <template v-else>
            <button
              @click="emit('openLogin')"
              class="flex items-center gap-1.5 px-3 py-1.5 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors"
            >
              <UserIcon class="w-4 h-4" />
              Login
            </button>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, defineEmits, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import { useAuth } from '../composables/useAuth';
import { ArrowLeftIcon, ArrowRightIcon, ArrowRightOnRectangleIcon, UserIcon, ChevronDownIcon } from '@heroicons/vue/24/outline';

const emit = defineEmits(['openLogin']);
const { isAuthenticated, logout } = useAuth();
const route = useRoute();

const isAdminRoute = computed(() => route.name === 'Admin' || route.path === '/admin');

const adminToggleLabel = computed(() => (isAdminRoute.value ? 'Front' : 'Back'));
const adminToggleTarget = computed(() => (isAdminRoute.value ? '/' : '/admin'));

const serverDropdownRef = ref(null);
const showServerDropdown = ref(false);

const servers = ref([
{
    id: 1,
    name: 'Hosting.com',
    url: 'https://my.hosting.com/login',
    icon: '../src/assets/hosting.com-64px.png',
  },
  {
    id: 1,
    name: 'Contabo.com',
    url: 'https://my.contabo.com/account/login',
    icon: '../src/assets/contabo-64px.png',
  },
  {
    id: 1,
    name: 'Cloud.mu',
    url: 'https://my.cloud.mu/index.php?rp=/login',
    icon: '../src/assets/cloud.mu-64px.png',
  },
  // Add more servers here as needed
  // {
  //   id: 2,
  //   name: 'Server 2',
  //   url: 'https://example.com',
  //   icon: '../assets/server2-icon.png',
  // },
]);

const toggleServerDropdown = () => {
  showServerDropdown.value = !showServerDropdown.value;
};

const closeServerDropdown = () => {
  showServerDropdown.value = false;
};

const handleClickOutside = (event) => {
  if (serverDropdownRef.value && !serverDropdownRef.value.contains(event.target)) {
    closeServerDropdown();
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

const handleLogout = async () => {
  await logout();
  window.location.href = '/';
};
</script>
