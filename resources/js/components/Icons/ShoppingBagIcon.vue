<template>
    <svg
        width="1em"
        height="1em"
        class="size-5"
        :class="className"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
