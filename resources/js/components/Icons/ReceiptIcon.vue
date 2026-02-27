<template>
    <svg
        width="1em"
        height="1em"
        class="size-5"
        :class="className"
        fill="currentColor"
        viewBox="0 0 256 256"
    >
        <path
            d="M216 80v128a8 8 0 0 1-8 8H48a8 8 0 0 1-8-8V80a8 8 0 0 1 8-8h160a8 8 0 0 1 8 8"
            opacity=".2"
        />
        <path
            d="M72 104a8 8 0 0 1 8-8h96a8 8 0 0 1 0 16H80a8 8 0 0 1-8-8m8 40h96a8 8 0 0 0 0-16H80a8 8 0 0 0 0 16m40 16H80a8 8 0 0 0 0 16h40a8 8 0 0 0 0-16m112-88v136a16 16 0 0 1-16 16H40a16 16 0 0 1-16-16V72a16 16 0 0 1 16-16h40v-8a8 8 0 0 1 16 0v8h64v-8a8 8 0 0 1 16 0v8h40a16 16 0 0 1 16 16m-16 0H40v136h176Z"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
