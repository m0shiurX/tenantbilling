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
            d="M20.5 14v-4c0-3.77 0-5.66-1.17-6.83S16.27 2 12.5 2h-1C7.73 2 5.84 2 4.67 3.17S3.5 6.23 3.5 10v4c0 3.77 0 5.66 1.17 6.83S7.73 22 11.5 22h1c3.77 0 5.66 0 6.83-1.17S20.5 17.77 20.5 14Z"
            opacity=".4"
        />
        <path
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M3.5 9v5c0 3.77 0 5.66 1.17 6.83S7.73 22 11.5 22h1c3.77 0 5.66 0 6.83-1.17S20.5 17.77 20.5 14v-4c0-3.77 0-5.66-1.17-6.83S16.27 2 12.5 2H12m1.5 15h4m-4-10h4m-4 5h4"
        />
        <path
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M6.5 16.5s.97.27 1.5 1.5c0 0 1-3 3-4m-1-9H3.5M10 5c0-.84-2.33-2.41-2.92-3M10 5c0 .84-2.33 2.41-2.92 3"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
