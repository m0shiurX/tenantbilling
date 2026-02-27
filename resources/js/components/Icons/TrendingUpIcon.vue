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
            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
        />
    </svg>
</template>
<script setup>
defineProps({
    className: String,
});
</script>
