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
            d="M224 88v16H32V88a8 8 0 0 1 8-8h176a8 8 0 0 1 8 8"
            opacity=".2"
        />
        <path
            d="M224 48H32a16 16 0 0 0-16 16v128a16 16 0 0 0 16 16h192a16 16 0 0 0 16-16V64a16 16 0 0 0-16-16m0 16v24H32V64Zm0 128H32v-88h192z"
        />
    </svg>
</template>
<script setup>
defineProps({
    className: String,
});
</script>
