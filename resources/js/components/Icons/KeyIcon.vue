<template>
    <svg
        width="1em"
        height="1em"
        class="size-5"
        :class="className"
        fill="currentColor"
        viewBox="0 0 256 256"
    >
        <path d="M160 76a52 52 0 1 1-52-52a52 52 0 0 1 52 52" opacity=".2" />
        <path
            d="M160.07 95.21a60 60 0 1 0-63.87 63.87l-37.42 37.43a8 8 0 0 0-2.35 5.67v39.38a8 8 0 0 0 8 8h40a8 8 0 0 0 8-8v-16h16a8 8 0 0 0 8-8v-16h16a8 8 0 0 0 5.66-2.34l9.42-9.43a60 60 0 0 0 .56-94.58M152 136a43.79 43.79 0 0 1-30.56-12.46a8 8 0 0 0-11.32 11.32A59.71 59.71 0 0 0 124.91 146l-51.22 51.24V184h-16a8 8 0 0 0-8 8v16H72v-16a8 8 0 0 0-8-8H48v17.76l42.34-42.34A8 8 0 0 0 79 148.09a44 44 0 1 1 73-12.09"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
