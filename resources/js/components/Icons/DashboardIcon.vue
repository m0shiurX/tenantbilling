<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        class="size-5"
        :class="className"
        width="1em"
        aria-hidden="true"
        role="img"
        height="1em"
        viewBox="0 0 24 24"
    >
        <path
            fill="currentColor"
            d="M14 3H9.5v18H14c3.28 0 4.92 0 6.08-.92.43-.33.8-.75 1.1-1.24.82-1.3.82-3.15.82-6.84s0-5.53-.81-6.84c-.3-.48-.68-.9-1.1-1.24C18.91 3 17.27 3 14 3Z"
            opacity=".4"
        />
        <path
            stroke="currentColor"
            d="M2 12c0-3.69 0-5.53.81-6.84.3-.48.68-.9 1.1-1.24C5.09 3 6.73 3 10 3h4c3.28 0 4.92 0 6.08.92.43.33.8.76 1.1 1.24C22 6.46 22 8.3 22 12s0 5.53-.81 6.84c-.3.48-.68.9-1.1 1.24-1.17.92-2.81.92-6.09.92h-4c-3.28 0-4.92 0-6.08-.92-.43-.33-.8-.75-1.1-1.24C2 17.54 2 15.7 2 12Z"
        />
        <path stroke="currentColor" stroke-linejoin="round" d="M9.5 3v18" />
        <path
            stroke="currentColor"
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M5 7h1m-1 3h1"
        />
    </svg>
</template>

<script setup>
defineProps({
    className: String,
});
</script>
