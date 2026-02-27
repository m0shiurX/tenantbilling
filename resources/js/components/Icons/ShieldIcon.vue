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
            d="M216 56v56c0 96-88 120-88 120s-88-24-88-120V56Z"
            opacity=".2"
        />
        <path
            d="M208 40H48a16 16 0 0 0-16 16v56c0 52.72 25.52 84.67 46.93 102.19c23.06 18.86 46 25.27 47 25.53a8 8 0 0 0 4.2 0c1-.26 23.91-6.67 47-25.53C198.48 196.67 224 164.72 224 112V56a16 16 0 0 0-16-16m0 72c0 37.07-13.66 67.16-40.6 89.42A129.24 129.24 0 0 1 128 223.62a128.22 128.22 0 0 1-39.4-22.2C61.66 179.16 48 149.07 48 112V56h160Z"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
