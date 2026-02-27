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
            d="M13 2.5h-1c-4.48 0-6.72 0-8.1 1.4C2.5 5.27 2.5 7.51 2.5 12c0 4.48 0 6.72 1.4 8.1 1.38 1.4 3.62 1.4 8.1 1.4 4.48 0 6.72 0 8.1-1.4 1.4-1.38 1.4-3.62 1.4-8.1v-1"
        />
        <path
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            d="m21.5 2.5-4.17 4.17m-.83-3.65.12 3.09c0 .72.44 1.18 1.23 1.23l3.12.15"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
