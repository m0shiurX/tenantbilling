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
            d="M208 88v120a8 8 0 0 1-8 8H56a8 8 0 0 1-8-8V40a8 8 0 0 1 8-8h96Z"
            opacity=".2"
        />
        <path
            d="m213.66 82.34l-56-56A8 8 0 0 0 152 24H56a16 16 0 0 0-16 16v176a16 16 0 0 0 16 16h144a16 16 0 0 0 16-16V88a8 8 0 0 0-2.34-5.66M160 51.31L188.69 80H160ZM200 216H56V40h88v48a8 8 0 0 0 8 8h48z"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
