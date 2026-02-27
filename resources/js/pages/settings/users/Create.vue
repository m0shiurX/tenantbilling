<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { BackArrowIcon } from '@/components/Icons';
import AppLayout from '@/layouts/AppLayout.vue';
import { type Role } from '@/types';

interface Props {
    roles: Role[];
    statuses: Array<{ value: string; label: string }>;
}

const props = defineProps<Props>();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    status: 'active',
    roles: [] as number[],
});

function submit() {
    form.post('/settings/users');
}
</script>

<template>
    <AppLayout>
        <Head title="Create User" />

        <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center gap-4">
                    <Link
                        href="/settings/users"
                        class="group rounded-xl border border-primary-light bg-linear-to-br from-primary-lighter to-primary-light/50 p-2.5 transition-all duration-300 hover:from-primary-light hover:to-primary-lighter hover:shadow-md hover:shadow-primary-light/50"
                    >
                        <BackArrowIcon
                            class="h-5 w-5 text-primary transition-transform duration-300 group-hover:-translate-x-0.5"
                        />
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-text">
                            Create User
                        </h1>
                        <p class="mt-1 text-sm text-text-muted">
                            Add a new team member to your organization
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div
                class="rounded-lg border border-white bg-white/50 p-6 shadow-sm shadow-surface-alt"
            >
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Name -->
                        <div>
                            <label
                                for="name"
                                class="mb-1 block text-sm font-medium text-text-muted"
                            >
                                Name <span class="text-danger">*</span>
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-lg border border-border px-3 py-2 focus:ring-2 focus:ring-primary focus:outline-none"
                                placeholder="Enter full name"
                                required
                            />
                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-sm text-danger"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label
                                for="email"
                                class="mb-1 block text-sm font-medium text-text-muted"
                            >
                                Email <span class="text-danger">*</span>
                            </label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="w-full rounded-lg border border-border px-3 py-2 focus:ring-2 focus:ring-primary focus:outline-none"
                                placeholder="user@example.com"
                                required
                            />
                            <p
                                v-if="form.errors.email"
                                class="mt-1 text-sm text-danger"
                            >
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Password -->
                        <div>
                            <label
                                for="password"
                                class="mb-1 block text-sm font-medium text-text-muted"
                            >
                                Password <span class="text-danger">*</span>
                            </label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="w-full rounded-lg border border-border px-3 py-2 focus:ring-2 focus:ring-primary focus:outline-none"
                                placeholder="••••••••"
                                required
                            />
                            <p
                                v-if="form.errors.password"
                                class="mt-1 text-sm text-danger"
                            >
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label
                                for="password_confirmation"
                                class="mb-1 block text-sm font-medium text-text-muted"
                            >
                                Confirm Password
                                <span class="text-danger">*</span>
                            </label>
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                class="w-full rounded-lg border border-border px-3 py-2 focus:ring-2 focus:ring-primary focus:outline-none"
                                placeholder="••••••••"
                                required
                            />
                        </div>

                        <!-- Status -->
                        <div>
                            <label
                                for="status"
                                class="mb-1 block text-sm font-medium text-text-muted"
                            >
                                Status
                            </label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full rounded-lg border border-border px-3 py-2 focus:ring-2 focus:ring-primary focus:outline-none"
                            >
                                <option
                                    v-for="s in statuses"
                                    :key="s.value"
                                    :value="s.value"
                                >
                                    {{ s.label }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.status"
                                class="mt-1 text-sm text-danger"
                            >
                                {{ form.errors.status }}
                            </p>
                        </div>
                    </div>

                    <!-- Roles -->
                    <div class="mt-6">
                        <label
                            class="mb-2 block text-sm font-medium text-text-muted"
                        >
                            Roles
                        </label>
                        <div
                            class="max-h-48 overflow-y-auto rounded-lg border border-border bg-white p-4"
                        >
                            <div
                                class="grid grid-cols-1 gap-2 lg:grid-cols-3 sm:grid-cols-2"
                            >
                                <label
                                    v-for="role in roles"
                                    :key="role.id"
                                    class="flex cursor-pointer items-center gap-2 rounded px-2 py-1 hover:bg-surface"
                                >
                                    <input
                                        type="checkbox"
                                        :value="role.id"
                                        v-model="form.roles"
                                        class="rounded border-border text-primary focus:ring-primary"
                                    />
                                    <span class="text-sm">{{ role.name }}</span>
                                </label>
                            </div>
                            <p
                                v-if="roles.length === 0"
                                class="text-sm text-text-muted"
                            >
                                No roles available
                            </p>
                        </div>
                        <p
                            v-if="form.errors.roles"
                            class="mt-1 text-sm text-danger"
                        >
                            {{ form.errors.roles }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div
                        class="mt-6 flex items-center gap-4 border-t border-border pt-6"
                    >
                        <button
                            type="submit"
                            class="rounded-lg bg-primary px-6 py-2 text-white transition hover:bg-primary-dark disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            {{
                                form.processing ? 'Creating...' : 'Create User'
                            }}
                        </button>
                        <Link
                            href="/settings/users"
                            class="text-text-muted hover:text-text"
                        >
                            Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
