<template>
  <div
    class="rounded-lg overflow-hidden shadow-sm border border-gray-200"
    :class="backgroundClass"
  >
    <div
      class="flex items-center justify-between px-4 py-2 border-b border-gray-200"
      :class="headerBgClass"
    >
      <div class="flex items-center gap-2">
        <component :is="statusIcon" class="w-5 h-5 text-white" />
        <h2 class="text-sm font-semibold uppercase tracking-wide text-white">
          {{ title }}
        </h2>
      </div>
      <span class="text-xs font-medium text-white/80">
        {{ projects.length }}
      </span>
    </div>

    <div class="relative p-4">
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

      <div
        v-if="!projects.length"
        class="pointer-events-none absolute inset-4 flex items-center justify-center"
      >
        <div
          class="w-full border-2 border-dashed border-gray-300/70 rounded-lg py-6 px-3 text-center text-xs text-gray-400 italic"
        >
          Drop cards here to move them to {{ title }}.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits, computed } from 'vue';
import {
  SparklesIcon,
  ArrowPathIcon,
  PauseCircleIcon,
  CheckCircleIcon,
  StopCircleIcon,
} from '@heroicons/vue/24/solid';
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
    on_hold: 'text-orange-600',
    completed: 'text-green-600',
    stopped: 'text-red-600',
  };
  return colors[props.status] || 'text-gray-600';
});

const backgroundClass = computed(() => {
  const classes = {
    new: 'bg-pattern-new',
    in_progress: 'bg-pattern-in-progress',
    on_hold: 'bg-pattern-on-hold',
    completed: 'bg-pattern-completed',
    stopped: 'bg-pattern-stopped',
  };
  return classes[props.status] || 'bg-gray-50';
});

const headerBgClass = computed(() => {
  const classes = {
    new: 'bg-blue-600',
    in_progress: 'bg-yellow-600',
    on_hold: 'bg-orange-600',
    completed: 'bg-green-600',
    stopped: 'bg-red-600',
  };
  return classes[props.status] || 'bg-gray-700';
});

const statusIcon = computed(() => {
  const icons = {
    new: SparklesIcon,
    in_progress: ArrowPathIcon,
    on_hold: PauseCircleIcon,
    completed: CheckCircleIcon,
    stopped: StopCircleIcon,
  };
  return icons[props.status] || SparklesIcon;
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

.bg-pattern-new {
  background-color: #eff6ff; /* blue-50 */
  background-image:
    linear-gradient(rgba(59, 130, 246, 0.07) 1px, transparent 1px),
    linear-gradient(90deg, rgba(59, 130, 246, 0.07) 1px, transparent 1px);
  background-size: 16px 16px;
}

.bg-pattern-in-progress {
  background-color: #fffbeb; /* amber-50 */
  background-image:
    linear-gradient(rgba(245, 158, 11, 0.08) 1px, transparent 1px),
    linear-gradient(90deg, rgba(245, 158, 11, 0.08) 1px, transparent 1px);
  background-size: 16px 16px;
}

.bg-pattern-on-hold {
  background-color: #fff7ed; /* orange-50 */
  background-image:
    linear-gradient(rgba(249, 115, 22, 0.08) 1px, transparent 1px),
    linear-gradient(90deg, rgba(249, 115, 22, 0.08) 1px, transparent 1px);
  background-size: 16px 16px;
}

.bg-pattern-completed {
  background-color: #ecfdf3; /* green-50 */
  background-image:
    linear-gradient(rgba(34, 197, 94, 0.08) 1px, transparent 1px),
    linear-gradient(90deg, rgba(34, 197, 94, 0.08) 1px, transparent 1px);
  background-size: 16px 16px;
}

.bg-pattern-stopped {
  background-color: #fef2f2; /* red-50 */
  background-image:
    linear-gradient(rgba(248, 113, 113, 0.08) 1px, transparent 1px),
    linear-gradient(90deg, rgba(248, 113, 113, 0.08) 1px, transparent 1px);
  background-size: 16px 16px;
}
</style>
