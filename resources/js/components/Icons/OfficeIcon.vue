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
            d="M216 216H40V40a8 8 0 0 1 8-8h160a8 8 0 0 1 8 8Z"
            opacity=".2"
        />
        <path
            d="M232 224h-24V40a16 16 0 0 0-16-16H64a16 16 0 0 0-16 16v184H24a8 8 0 0 0 0 16h208a8 8 0 0 0 0-16M64 40h128v184H64Zm40 64a8 8 0 0 1-8 8H80a8 8 0 0 1 0-16h16a8 8 0 0 1 8 8m0-32a8 8 0 0 1-8 8H80a8 8 0 0 1 0-16h16a8 8 0 0 1 8 8m0 64a8 8 0 0 1-8 8H80a8 8 0 0 1 0-16h16a8 8 0 0 1 8 8m0 32a8 8 0 0 1-8 8H80a8 8 0 0 1 0-16h16a8 8 0 0 1 8 8m24-64a8 8 0 0 1 8-8h16a8 8 0 0 1 0 16h-16a8 8 0 0 1-8-8m0-32a8 8 0 0 1 8-8h16a8 8 0 0 1 0 16h-16a8 8 0 0 1-8-8m0 64a8 8 0 0 1 8-8h16a8 8 0 0 1 0 16h-16a8 8 0 0 1-8-8m0 32a8 8 0 0 1 8-8h16a8 8 0 0 1 0 16h-16a8 8 0 0 1-8-8"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
