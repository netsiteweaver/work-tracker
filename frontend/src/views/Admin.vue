<template>
  <div class="min-h-screen bg-gray-100">
    <FloatingBar @open-login="showLoginModal = true" />

    <div class="container mx-auto px-4 py-8">
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold text-gray-800">Project Management</h1>
          <button
            @click="openCreateModal"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
          >
            + New Project
          </button>
        </div>

        <div v-if="loading" class="text-center py-12">
          <p class="text-gray-600">Loading projects...</p>
        </div>

        <div v-else-if="error" class="text-center py-12">
          <p class="text-red-600">{{ error }}</p>
        </div>

        <div v-else class="space-y-4">
          <ProjectCard
            v-for="project in projects"
            :key="project.id"
            :project="project"
            :show-actions="true"
            @edit="openEditModal"
            @delete="handleDelete"
          />
        </div>
      </div>
    </div>

    <LoginModal :is-open="showLoginModal" @close="showLoginModal = false" />

    <!-- Project Form Modal -->
    <Teleport to="body">
      <div
        v-if="showFormModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto"
        @click.self="closeFormModal"
      >
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 my-8">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">
              {{ editingProject ? 'Edit Project' : 'New Project' }}
            </h2>
            <button
              @click="closeFormModal"
              class="text-gray-500 hover:text-gray-700 text-2xl leading-none"
            >
              &times;
            </button>
          </div>

          <form @submit.prevent="handleSubmit" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
              <input
                v-model="formData.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
              <textarea
                v-model="formData.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              ></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dev Path</label>
                <input
                  v-model="formData.dev_path"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                <select
                  v-model="formData.status"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="new">New</option>
                  <option value="in_progress">In Progress</option>
                  <option value="on_hold">On Hold</option>
                  <option value="completed">Completed</option>
                  <option value="stopped">Stopped</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Staging URL</label>
              <input
                v-model="formData.staging_url"
                type="url"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Production URL</label>
              <input
                v-model="formData.production_url"
                type="url"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input
                  v-model="formData.start_date"
                  type="date"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Finish Date</label>
                <input
                  v-model="formData.finish_date"
                  type="date"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
            </div>

            <div v-if="formError" class="text-red-600 text-sm">{{ formError }}</div>

            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="closeFormModal"
                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="submitting"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ submitting ? 'Saving...' : 'Save' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import FloatingBar from '../components/FloatingBar.vue';
import ProjectCard from '../components/ProjectCard.vue';
import LoginModal from '../components/LoginModal.vue';
import { useAuth } from '../composables/useAuth';
import api from '../services/api';

const router = useRouter();
const { isAuthenticated } = useAuth();

const projects = ref([]);
const loading = ref(true);
const error = ref('');
const showLoginModal = ref(false);
const showFormModal = ref(false);
const editingProject = ref(null);
const submitting = ref(false);
const formError = ref('');

const formData = ref({
  name: '',
  description: '',
  dev_path: '',
  staging_url: '',
  production_url: '',
  status: 'new',
  start_date: '',
  finish_date: '',
});

const fetchProjects = async () => {
  loading.value = true;
  error.value = '';
  try {
    const response = await api.getProjects();
    projects.value = response.data;
  } catch (err) {
    console.error('Error fetching projects:', err);
    error.value = 'Failed to load projects. Please try again.';
  } finally {
    loading.value = false;
  }
};

const openCreateModal = () => {
  editingProject.value = null;
  formData.value = {
    name: '',
    description: '',
    dev_path: '',
    staging_url: '',
    production_url: '',
    status: 'new',
    start_date: '',
    finish_date: '',
  };
  formError.value = '';
  showFormModal.value = true;
};

const openEditModal = (project) => {
  editingProject.value = project;
  formData.value = {
    name: project.name,
    description: project.description || '',
    dev_path: project.dev_path || '',
    staging_url: project.staging_url || '',
    production_url: project.production_url || '',
    status: project.status,
    start_date: project.start_date || '',
    finish_date: project.finish_date || '',
  };
  formError.value = '';
  showFormModal.value = true;
};

const closeFormModal = () => {
  showFormModal.value = false;
  editingProject.value = null;
  formError.value = '';
};

const handleSubmit = async () => {
  submitting.value = true;
  formError.value = '';

  try {
    if (editingProject.value) {
      await api.updateProject(editingProject.value.id, formData.value);
    } else {
      await api.createProject(formData.value);
    }
    await fetchProjects();
    closeFormModal();
  } catch (err) {
    console.error('Error saving project:', err);
    formError.value = err.response?.data?.message || 'Failed to save project';
  } finally {
    submitting.value = false;
  }
};

const handleDelete = async (project) => {
  if (!confirm(`Are you sure you want to delete "${project.name}"?`)) {
    return;
  }

  try {
    await api.deleteProject(project.id);
    await fetchProjects();
  } catch (err) {
    console.error('Error deleting project:', err);
    alert('Failed to delete project');
  }
};

onMounted(async () => {
  if (!isAuthenticated.value) {
    showLoginModal.value = true;
    setTimeout(() => {
      router.push('/');
    }, 2000);
    return;
  }
  await fetchProjects();
});
</script>
