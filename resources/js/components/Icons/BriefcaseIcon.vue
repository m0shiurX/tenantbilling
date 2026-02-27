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
            d="M224 72H32v128a8 8 0 0 0 8 8h176a8 8 0 0 0 8-8Z"
            opacity=".2"
        />
        <path
            d="M216 64h-40v-8a24 24 0 0 0-24-24h-48a24 24 0 0 0-24 24v8H40a16 16 0 0 0-16 16v128a16 16 0 0 0 16 16h176a16 16 0 0 0 16-16V80a16 16 0 0 0-16-16M96 56a8 8 0 0 1 8-8h48a8 8 0 0 1 8 8v8H96Zm120 152H40V80h176Zm-48-72a8 8 0 0 1-8 8h-64a8 8 0 0 1 0-16h64a8 8 0 0 1 8 8"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
