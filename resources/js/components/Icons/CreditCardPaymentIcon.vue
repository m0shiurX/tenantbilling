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
            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
