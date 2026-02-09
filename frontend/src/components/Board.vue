<template>
  <div class="bg-gray-50 rounded-lg p-4">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-xl font-bold" :class="titleColor">{{ title }}</h2>
      <span class="text-sm font-medium text-gray-600">{{ projects.length }}</span>
    </div>

    <draggable
      :list="projects"
      :group="{ name: 'projects', pull: true, put: true }"
      :animation="200"
      ghost-class="ghost-card"
      @change="handleChange"
      item-key="id"
      class="min-h-[200px] space-y-2"
    >
      <template #item="{ element }">
        <ProjectCard :project="element" :show-actions="false" />
      </template>
    </draggable>
  </div>
</template>

<script setup>
import { defineProps, defineEmits, computed } from 'vue';
import draggable from 'vuedraggable';
import ProjectCard from './ProjectCard.vue';

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  projects: {
    type: Array,
    required: true,
  },
  status: {
    type: String,
    required: true,
  },
});

const emit = defineEmits(['update']);

const titleColor = computed(() => {
  const colors = {
    new: 'text-blue-600',
    in_progress: 'text-yellow-600',
    completed: 'text-green-600',
    stopped: 'text-red-600',
  };
  return colors[props.status] || 'text-gray-600';
});

const handleChange = (event) => {
  emit('update', event);
};
</script>

<style scoped>
.ghost-card {
  opacity: 0.5;
  background: #f0f0f0;
}
</style>
