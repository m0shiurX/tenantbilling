<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        width="1em"
        height="1em"
        class="size-5"
        :class="className"
        color="currentColor"
        viewBox="0 0 24 24"
    >
        <path
            fill="currentColor"
            fill-rule="evenodd"
            d="M12 8a4 4 0 1 0 4 4 1 1 0 1 1 2 0 6 6 0 1 1-6-6 1 1 0 1 1 0 2Z"
            clip-rule="evenodd"
        />
        <path
            fill="currentColor"
            fill-rule="evenodd"
            d="M12 3a9 9 0 1 0 8.82 7.199 1 1 0 1 1 1.96-.398c.144.711.22 1.447.22 2.199 0 6.075-4.925 11-11 11S1 18.075 1 12 5.925 1 12 1c.752 0 1.488.076 2.199.22a1 1 0 1 1-.398 1.96A9.046 9.046 0 0 0 12 3Z"
            clip-rule="evenodd"
            opacity=".4"
        />
        <path
            fill="currentColor"
            d="M20.142 2.078a1.43 1.43 0 0 0-1.17-1.059 1.603 1.603 0 0 0-1.381.464l-1.15 1.15c-1.269 1.269-1.375 2.833-.95 4.462l-4.198 4.198a1 1 0 0 0 1.414 1.414l4.198-4.198c1.629.425 3.193.319 4.463-.95l1.149-1.15a1.6 1.6 0 0 0 .464-1.381 1.43 1.43 0 0 0-1.059-1.17L20.51 3.49l-.368-1.412Z"
        />
    </svg>
</template>
<script setup>
defineProps({
    className: String,
});
</script>
