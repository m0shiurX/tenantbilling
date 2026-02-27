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
            d="M14 6.5h-4c-3.287 0-4.931 0-6.038.908a4 4 0 0 0-.554.554C2.5 9.07 2.5 10.712 2.5 14v-3.5c0-3.771 0-5.657 1.172-6.828C4.843 2.5 6.729 2.5 10.5 2.5h3c3.771 0 5.657 0 6.828 1.172C21.5 4.843 21.5 6.729 21.5 10.5V14c0-3.287 0-4.931-.908-6.038a4 4 0 0 0-.554-.554C18.93 6.5 17.288 6.5 14 6.5Z"
            opacity=".4"
        />
        <path
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M14 21.5h-4c-3.287 0-4.931 0-6.038-.908a4 4 0 0 1-.554-.554C2.5 18.93 2.5 17.288 2.5 14c0-3.287 0-4.931.908-6.038a4 4 0 0 1 .554-.554C5.07 6.5 6.712 6.5 10 6.5h4c3.288 0 4.931 0 6.038.908a4 4 0 0 1 .554.554c.908 1.107.908 2.75.908 6.038 0 3.288 0 4.931-.908 6.038a4.001 4.001 0 0 1-.554.554c-1.107.908-2.75.908-6.038.908Z"
        />
        <path
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M2.5 14.5v-4c0-3.771 0-5.657 1.172-6.828C4.843 2.5 6.729 2.5 10.5 2.5h3c3.771 0 5.657 0 6.828 1.172C21.5 4.843 21.5 6.729 21.5 10.5v4m-6.5 0s-2.21 3-3 3-3-3-3-3m3 2.5v-6.5"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
