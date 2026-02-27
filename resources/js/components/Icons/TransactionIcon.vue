<template>
    <svg
        class="size-5"
        :class="className"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M17 13a1 1 0 0 1 0 2H5l4 3v2H7l-5-5 1-2h14Zm0-9 5 5-1 2H7a1 1 0 0 1 0-2h12l-4-3V4h2Z"
            fill="currentColor"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
