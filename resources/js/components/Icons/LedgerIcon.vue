<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        class="size-5"
        :class="className"
        width="1em"
        aria-hidden="true"
        role="img"
        height="1em"
        viewBox="0 0 24 24"
    >
        <path
            fill="currentColor"
            d="M11.5 21c-4.48 0-6.72 0-8.1-1.4C2 18.23 2 15.99 2 11.5c0-1.85 0-3.32.1-4.5h18.8c.1 1.18.1 2.65.1 4.5 0 1.92 0 3.43-.11 4.64a3.6 3.6 0 1 0-4.75 4.75c-1.21.11-2.72.11-4.64.11Z"
            opacity=".4"
        />
        <path
            stroke="currentColor"
            stroke-linecap="round"
            d="M11.5 21c-4.48 0-6.72 0-8.1-1.4C2 18.23 2 15.99 2 11.5c0-4.48 0-6.72 1.4-8.1C4.77 2 7.01 2 11.5 2c4.48 0 6.72 0 8.1 1.4C21 4.77 21 7.01 21 11.5"
        />
        <path stroke="currentColor" stroke-linejoin="round" d="M2 7h19" />
        <path
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M10 16h1m-5 0h1m3-4h4m-8 0h1m13.4 8.4L22 22m-.8-4.4a3.6 3.6 0 1 0-7.2 0 3.6 3.6 0 0 0 7.2 0Z"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
