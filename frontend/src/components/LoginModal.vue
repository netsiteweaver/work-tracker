<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-bold text-gray-800">
            {{ isLoginMode ? 'Login' : 'Register' }}
          </h2>
          <button
            @click="closeModal"
            class="text-gray-500 hover:text-gray-700 text-2xl leading-none"
          >
            &times;
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div v-if="!isLoginMode">
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input
              v-model="formData.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
              v-model="formData.email"
              type="email"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input
              v-model="formData.password"
              type="password"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div v-if="!isLoginMode">
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <input
              v-model="formData.password_confirmation"
              type="password"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div v-if="error" class="text-red-600 text-sm">{{ error }}</div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loading ? 'Please wait...' : (isLoginMode ? 'Login' : 'Register') }}
          </button>
        </form>

        <div class="mt-4 text-center text-sm">
          <button
            @click="toggleMode"
            class="text-blue-600 hover:text-blue-800"
          >
            {{ isLoginMode ? "Don't have an account? Register" : 'Already have an account? Login' }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue';
import { useAuth } from '../composables/useAuth';

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['close', 'success']);

const { login, register } = useAuth();

const isLoginMode = ref(true);
const loading = ref(false);
const error = ref('');

const formData = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const toggleMode = () => {
  isLoginMode.value = !isLoginMode.value;
  error.value = '';
  formData.value = {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
  };
};

const closeModal = () => {
  emit('close');
  error.value = '';
  formData.value = {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
  };
};

const handleSubmit = async () => {
  loading.value = true;
  error.value = '';

  let result;
  if (isLoginMode.value) {
    result = await login(formData.value.email, formData.value.password);
  } else {
    result = await register(
      formData.value.name,
      formData.value.email,
      formData.value.password,
      formData.value.password_confirmation
    );
  }

  loading.value = false;

  if (result.success) {
    emit('success');
    closeModal();
  } else {
    error.value = result.error || 'An error occurred';
  }
};
</script>
