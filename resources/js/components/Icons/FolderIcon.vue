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
            d="M224 88v112a8 8 0 0 1-8 8H40a8 8 0 0 1-8-8V64a8 8 0 0 1 8-8h53.33l28.8 36H216a8 8 0 0 1 8 8"
            opacity=".2"
        />
        <path
            d="M216 72h-84.69L104 44.69A15.86 15.86 0 0 0 92.69 40H40a16 16 0 0 0-16 16v144.62A15.4 15.4 0 0 0 39.38 216h177.51A15.13 15.13 0 0 0 232 200.89V88a16 16 0 0 0-16-16M40 56h52.69l18.89 18.89L92.69 56H40Zm176 144H40V88h176Z"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
