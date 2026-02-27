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
            d="M208 192H48a8 8 0 0 1 0-16l16-16V112a64 64 0 0 1 128 0v48l16 16a8 8 0 0 1 0 16"
            opacity=".2"
        />
        <path
            d="M221.8 175.94c-5.55-9.56-13.8-36.61-13.8-63.94a80 80 0 1 0-160 0c0 27.33-8.25 54.38-13.8 63.94A16 16 0 0 0 48 200h40.81a40 40 0 0 0 78.38 0H208a16 16 0 0 0 13.8-24.06M128 216a24 24 0 0 1-22.62-16h45.24A24 24 0 0 1 128 216m-80-32c7.7-13.24 16-43.92 16-72a64 64 0 1 1 128 0c0 28.08 8.3 58.76 16 72Z"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
