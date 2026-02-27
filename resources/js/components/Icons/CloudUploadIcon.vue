<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        width="1em"
        height="1em"
        class="size-5"
        :class="className"
        fill="none"
        color="currentColor"
        viewBox="0 0 24 24"
    >
        <path
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M17.478 9.011h.022c2.485 0 4.5 2.018 4.5 4.508 0 2.32-1.75 4.232-4 4.481m-.522-8.989A5.505 5.505 0 0 0 12 3a5.505 5.505 0 0 0-5.48 5.032m10.958.98a5.49 5.49 0 0 1-1.235 3.005M6.52 8.032A5.006 5.006 0 0 0 2 13.018a5.009 5.009 0 0 0 4 4.91m.52-9.896a4.972 4.972 0 0 1 3.48.98M12 13v8m0-8c-.7 0-2.008 1.994-2.5 2.5M12 13c.7 0 2.008 1.994 2.5 2.5"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
