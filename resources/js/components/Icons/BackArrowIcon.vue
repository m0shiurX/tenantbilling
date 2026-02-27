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
            d="M15 19l-7-7 7-7"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
