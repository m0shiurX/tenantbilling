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
            d="M2.5 12c0-4.48 0-6.72 1.4-8.1C5.27 2.5 7.51 2.5 12 2.5c4.48 0 6.72 0 8.1 1.4 1.4 1.38 1.4 3.62 1.4 8.1 0 4.48 0 6.72-1.4 8.1-1.38 1.4-3.62 1.4-8.1 1.4-4.48 0-6.72 0-8.1-1.4-1.4-1.38-1.4-3.62-1.4-8.1Z"
            opacity=".4"
        />
        <path
            stroke="currentColor"
            stroke-linecap="round"
            d="M12 9c-1.1 0-2 .67-2 1.5s.9 1.5 2 1.5 2 .67 2 1.5-.9 1.5-2 1.5m0-6c.87 0 1.61.42 1.89 1M12 9V8m0 7c-.87 0-1.61-.42-1.88-1M12 15v1"
        />
        <path
            stroke="currentColor"
            stroke-linecap="round"
            d="M13.5 2.5H12c-4.48 0-6.72 0-8.1 1.4C2.5 5.27 2.5 7.51 2.5 12c0 4.48 0 6.72 1.4 8.1 1.38 1.4 3.62 1.4 8.1 1.4 4.48 0 6.72 0 8.1-1.4 1.4-1.38 1.4-3.62 1.4-8.1v-1.5"
        />
        <path
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            d="m16.5 7.5 4.18-4.18m.82 3.66-.12-3.1c0-.72-.43-1.18-1.22-1.24l-3.13-.14"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
