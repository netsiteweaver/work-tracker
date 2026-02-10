<template>
  <div class="min-h-screen bg-gray-100">
    <FloatingBar @open-login="showLoginModal = true" />

    <div class="container mx-auto px-4 py-8">
      <div v-if="!isAuthenticated" class="text-center py-12">
        <p class="text-gray-600 text-lg mb-4">Please log in to view your projects.</p>
        <button
          @click="showLoginModal = true"
          class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
        >
          Login
        </button>
      </div>

      <div v-else-if="loading" class="text-center py-12">
        <p class="text-gray-600">Loading projects...</p>
      </div>

      <div v-else-if="error" class="text-center py-12">
        <p class="text-red-600">{{ error }}</p>
        <button
          @click="fetchProjects"
          class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          Retry
        </button>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <Board
          title="New"
          status="new"
          :projects="projectsByStatus.new"
          @update="handleProjectMove"
        />
        <Board
          title="In Progress"
          status="in_progress"
          :projects="projectsByStatus.in_progress"
          @update="handleProjectMove"
        />
        <Board
          title="On Hold"
          status="on_hold"
          :projects="projectsByStatus.on_hold"
          @update="handleProjectMove"
        />
        <Board
          title="Completed"
          status="completed"
          :projects="projectsByStatus.completed"
          @update="handleProjectMove"
        />
        <Board
          title="Stopped"
          status="stopped"
          :projects="projectsByStatus.stopped"
          @update="handleProjectMove"
        />
      </div>
    </div>

    <LoginModal :is-open="showLoginModal" @close="showLoginModal = false" @success="onLoginSuccess" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import FloatingBar from '../components/FloatingBar.vue';
import Board from '../components/Board.vue';
import LoginModal from '../components/LoginModal.vue';
import api from '../services/api';
import { useAuth } from '../composables/useAuth';

const { isAuthenticated } = useAuth();
const projects = ref([]);
const loading = ref(true);
const error = ref('');
const showLoginModal = ref(false);

const projectsByStatus = computed(() => {
  return {
    new: projects.value.filter((p) => p.status === 'new'),
    in_progress: projects.value.filter((p) => p.status === 'in_progress'),
    on_hold: projects.value.filter((p) => p.status === 'on_hold'),
    completed: projects.value.filter((p) => p.status === 'completed'),
    stopped: projects.value.filter((p) => p.status === 'stopped'),
  };
});

const fetchProjects = async () => {
  if (!isAuthenticated.value) {
    loading.value = false;
    return;
  }
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

const handleProjectMove = async (event) => {
  // Find the moved project and update its status
  if (event.added) {
    const movedProject = event.added.element;
    const newStatus = findProjectStatus(movedProject.id);
    
    if (newStatus && movedProject.status !== newStatus) {
      movedProject.status = newStatus;
      
      // Update sort orders
      const updatedProjects = [];
      Object.keys(projectsByStatus.value).forEach((status) => {
        projectsByStatus.value[status].forEach((project, index) => {
          updatedProjects.push({
            id: project.id,
            status: status,
            sort_order: index,
          });
        });
      });

      try {
        await api.updateProjectOrder(updatedProjects);
      } catch (err) {
        console.error('Error updating project order:', err);
        // Revert on error
        await fetchProjects();
      }
    }
  }
};

const findProjectStatus = (projectId) => {
  for (const [status, projectList] of Object.entries(projectsByStatus.value)) {
    if (projectList.some((p) => p.id === projectId)) {
      return status;
    }
  }
  return null;
};

const onLoginSuccess = () => {
  // Optionally refresh projects after login
  fetchProjects();
};

onMounted(() => {
  if (isAuthenticated.value) {
    fetchProjects();
  } else {
    loading.value = false;
  }
});
</script>
