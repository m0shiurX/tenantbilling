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
            d="M10 10.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm6 5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm4-8a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"
            opacity=".4"
        />
        <circle cx="8.5" cy="10.5" r="1.5" stroke="currentColor" />
        <circle cx="14.5" cy="15.5" r="1.5" stroke="currentColor" />
        <circle cx="18.5" cy="7.5" r="1.5" stroke="currentColor" />
        <path
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M15.43 14.3 18 9m-8.42 2.57 3.62 2.73M3 19l4.59-7.12"
        />
        <path
            stroke="currentColor"
            stroke-linecap="round"
            d="M20 21H9c-3.3 0-4.95 0-5.97-1.03C2 18.95 2 17.3 2 14V3"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
