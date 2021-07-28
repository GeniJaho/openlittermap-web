<template>
    <span
        @click="$emit('click', $event)"
        class="inline-flex rounded-full items-center py-0.5 pr-2.5 mr-2 sm:mr-3 my-1 text-sm font-medium bg-indigo-100 text-indigo-700 hover:bg-indigo-200 cursor-pointer"
        :class="showIcon ? 'pl-1' : 'pl-2.5'"
    >
        <button
            v-if="showIcon"
            type="button"
            class="flex-shrink-0 mr-0.5 h-4 w-4 rounded-full inline-flex items-center justify-center text-indigo-400 hover:bg-indigo-200 hover:text-indigo-500 focus:outline-none focus:bg-indigo-500 focus:text-white"
        >
            <span class="sr-only">Remove tag</span>
            <svg class="h-2 w-2" stroke="currentColor" fill="none" viewBox="0 0 8 8">
                <path stroke-linecap="round" stroke-width="1.5" d="M1 1l6 6m0-6L1 7"/>
            </svg>
        </button>
        {{ title }}
    </span>
</template>

<script>
export default {
    name: 'Badge',
    props: {
        title: String,
        showIcon: {
            type: Boolean,
            default: true
        }
    },
};
</script>

<style scoped>

</style>
