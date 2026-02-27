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
            d="M200 32H56a8 8 0 0 0-8 8v176a8 8 0 0 0 8 8h144a8 8 0 0 0 8-8V40a8 8 0 0 0-8-8m-24 48H80V56h96Z"
            opacity=".2"
        />
        <path
            d="M80 120a12 12 0 1 1 12 12a12 12 0 0 1-12-12m44 12a12 12 0 1 0-12-12a12 12 0 0 0 12 12m40-24a12 12 0 1 0 12 12a12 12 0 0 0-12-12m-64 56a12 12 0 1 0 12 12a12 12 0 0 0-12-12m24 0a12 12 0 1 0 12 12a12 12 0 0 0-12-12m40 0a12 12 0 1 0 12 12a12 12 0 0 0-12-12M200 24H56a16 16 0 0 0-16 16v176a16 16 0 0 0 16 16h144a16 16 0 0 0 16-16V40a16 16 0 0 0-16-16m0 192H56V40h144ZM72 88V56a8 8 0 0 1 8-8h96a8 8 0 0 1 8 8v32a8 8 0 0 1-8 8H80a8 8 0 0 1-8-8m16-8h80V64H88Z"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
