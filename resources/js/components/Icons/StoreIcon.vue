<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        aria-hidden="true"
        class="size-5"
        :class="className"
        viewBox="0 0 24 24"
    >
        <path
            fill="currentColor"
            d="M14.69 21h-5c-2.83 0-4.24 0-5.12-.88-.88-.88-.88-2.3-.88-5.12V9.86c.28.09.57.14.88.14a2.56 2.56 0 0 0 2.62-2.5 2.5 2.5 0 0 0 5 0 2.5 2.5 0 0 0 5 0c0 1.38 1.17 2.5 2.62 2.5.31 0 .6-.05.88-.14V15c0 2.83 0 4.24-.88 5.12-.88.88-2.3.88-5.12.88Z"
            opacity=".4"
        />
        <path
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M3.69 10v5c0 2.83 0 4.24.88 5.12.88.88 2.3.88 5.12.88h5c2.83 0 4.24 0 5.12-.88.88-.88.88-2.3.88-5.12v-5"
        />
        <path
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M17.19 7.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 1 1-5 0c0 1.38-1.17 2.5-2.62 2.5-.78 0-1.48-.32-1.96-.84-.82-.88-.3-2.19.4-3.17l.38-.53c.88-1.2 1.33-1.81 1.97-2.14C6 3 6.74 3 8.24 3h7.9c1.5 0 2.25 0 2.89.33.64.32 1.08.92 1.96 2.13l.39.53c.68.99 1.22 2.29.39 3.17-.48.52-1.18.84-1.96.84a2.56 2.56 0 0 1-2.62-2.5Zm-2 9.5c-.69.6-1.77 1-3 1s-2.32-.4-3-1"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
