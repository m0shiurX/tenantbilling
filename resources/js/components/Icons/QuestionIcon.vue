<template>
    <svg
        class="size-7"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M10 9a2 2 0 1 1 2 2v1m2 7-1 2h-2l-1-2H7c-2 0-4-2-4-4V7c0-2 2-4 4-4h10c2 0 4 2 4 4v8c0 2-2 4-4 4h-3Z"
            stroke="currentColor"
            stroke-width="1"
            stroke-linecap="round"
            stroke-linejoin="round"
        />
        <circle cx="12" cy="15" r="1" fill="currentColor" />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
