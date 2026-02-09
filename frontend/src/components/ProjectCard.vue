<template>
  <div class="bg-white rounded-lg shadow-md p-4 mb-3 cursor-move hover:shadow-lg transition-shadow border-l-4"
       :class="statusBorderColor">
    <div class="flex justify-between items-start mb-2">
      <h3 class="text-lg font-semibold text-gray-800">{{ project.name }}</h3>
      <span class="text-xs px-2 py-1 rounded-full" :class="statusBadgeColor">
        {{ formatStatus(project.status) }}
      </span>
    </div>

    <p v-if="project.description" class="text-sm text-gray-600 mb-3 line-clamp-2">
      {{ project.description }}
    </p>

    <div class="space-y-1 text-xs text-gray-500">
      <div v-if="project.dev_path" class="flex items-center">
        <span class="font-medium mr-1">Dev:</span>
        <span class="truncate">{{ project.dev_path }}</span>
      </div>
      <div v-if="project.staging_url" class="flex items-center">
        <span class="font-medium mr-1">Staging:</span>
        <a :href="project.staging_url" target="_blank" rel="noopener noreferrer"
           class="text-blue-600 hover:underline truncate">
          {{ project.staging_url }}
        </a>
      </div>
      <div v-if="project.production_url" class="flex items-center">
        <span class="font-medium mr-1">Production:</span>
        <a :href="project.production_url" target="_blank" rel="noopener noreferrer"
           class="text-blue-600 hover:underline truncate">
          {{ project.production_url }}
        </a>
      </div>
      <div v-if="project.start_date" class="flex items-center">
        <span class="font-medium mr-1">Started:</span>
        <span>{{ formatDate(project.start_date) }}</span>
      </div>
      <div v-if="project.finish_date" class="flex items-center">
        <span class="font-medium mr-1">Finished:</span>
        <span>{{ formatDate(project.finish_date) }}</span>
      </div>
    </div>

    <div v-if="showActions" class="mt-3 flex justify-end space-x-2">
      <button
        @click="emit('edit', project)"
        class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700"
      >
        Edit
      </button>
      <button
        @click="emit('delete', project)"
        class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700"
      >
        Delete
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed, defineProps, defineEmits } from 'vue';

const props = defineProps({
  project: {
    type: Object,
    required: true,
  },
  showActions: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['edit', 'delete']);

const statusBorderColor = computed(() => {
  const colors = {
    new: 'border-blue-500',
    in_progress: 'border-yellow-500',
    completed: 'border-green-500',
    stopped: 'border-red-500',
  };
  return colors[props.project.status] || 'border-gray-500';
});

const statusBadgeColor = computed(() => {
  const colors = {
    new: 'bg-blue-100 text-blue-800',
    in_progress: 'bg-yellow-100 text-yellow-800',
    completed: 'bg-green-100 text-green-800',
    stopped: 'bg-red-100 text-red-800',
  };
  return colors[props.project.status] || 'bg-gray-100 text-gray-800';
});

const formatStatus = (status) => {
  return status.replace('_', ' ').replace(/\b\w/g, (l) => l.toUpperCase());
};

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString();
};
</script>
