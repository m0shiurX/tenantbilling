<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AccountPasswordController from '@/actions/App/Http/Controllers/Account/PasswordController';
import AccountProfileController from '@/actions/App/Http/Controllers/Account/ProfileController';
import {
    BackArrowIcon,
    SpinnerIcon,
    SuccessCheckIcon,
} from '@/components/Icons';
import AppLayout from '@/layouts/AppLayout.vue';
import { send } from '@/routes/verification';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
};

defineProps<Props>();

const page = usePage();
const user = page.props.auth.user;

// Compute initials for avatar
const initials = computed(() => {
    const name = user.name || '';
    const parts = name.split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return name.slice(0, 2).toUpperCase();
});
</script>

<template>
    <AppLayout>
        <Head title="Profile" />

        <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center gap-3">
                    <Link
                        href="/account"
                        class="rounded-lg bg-gray-100 p-2 transition-colors hover:bg-gray-200"
                    >
                        <BackArrowIcon class="h-5 w-5 text-gray-600" />
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            My Account
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Manage your profile and security settings
                        </p>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            <div
                v-if="$page.props.flash?.success"
                class="mb-6 flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800"
            >
                <SuccessCheckIcon class="h-5 w-5 text-green-500" />
                {{ $page.props.flash.success }}
            </div>

            <!-- Account Info Card (Read-only) -->
            <div
                class="mb-6 overflow-hidden rounded-lg border border-white bg-white/50 shadow-sm shadow-gray-50"
            >
                <div
                    class="border-b border-gray-100 bg-linear-to-r from-gray-50 to-slate-50 px-6 py-4"
                >
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-gray-100 p-2">
                            <svg
                                class="h-5 w-5 text-gray-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">
                                Account Information
                            </h3>
                            <p class="text-sm text-gray-500">
                                Your account details
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex flex-col items-center gap-6 sm:flex-row">
                        <!-- Avatar -->
                        <div
                            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-emerald-500 to-emerald-600 sm:h-20 sm:w-20"
                        >
                            <span
                                class="text-xl font-bold text-white sm:text-2xl"
                                >{{ initials }}</span
                            >
                        </div>

                        <div
                            class="grid w-full flex-1 grid-cols-2 gap-4 text-center sm:grid-cols-4 sm:text-left"
                        >
                            <div>
                                <p
                                    class="text-xs tracking-wider text-gray-500 uppercase"
                                >
                                    Name
                                </p>
                                <p class="truncate font-medium text-gray-900">
                                    {{ user.name }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs tracking-wider text-gray-500 uppercase"
                                >
                                    Email
                                </p>
                                <p class="truncate font-medium text-gray-900">
                                    {{ user.email }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs tracking-wider text-gray-500 uppercase"
                                >
                                    Member Since
                                </p>
                                <p class="font-medium text-gray-900">
                                    {{ user.created_at }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="text-xs tracking-wider text-gray-500 uppercase"
                                >
                                    Account ID
                                </p>
                                <p class="font-medium text-gray-900">
                                    #{{ user.id }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Information Form -->
            <Form
                v-bind="AccountProfileController.update.form()"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <div
                    class="mb-6 overflow-hidden rounded-lg border border-white bg-white/50 shadow-sm shadow-gray-50"
                >
                    <div
                        class="border-b border-gray-100 bg-linear-to-r from-emerald-50 to-amber-50 px-6 py-4"
                    >
                        <div class="flex items-center gap-3">
                            <div class="rounded-lg bg-emerald-100 p-2">
                                <svg
                                    class="h-5 w-5 text-emerald-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">
                                    Profile Information
                                </h3>
                                <p class="text-sm text-gray-500">
                                    Update your account's profile information
                                    and email address
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6 p-6">
                        <!-- Name -->
                        <div>
                            <label
                                for="name"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                :value="user.name"
                                class="w-full rounded-lg border-gray-300 px-4 py-3 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                :class="{ 'border-red-500': errors.name }"
                                placeholder="Enter your name"
                            />
                            <p
                                v-if="errors.name"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ errors.name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label
                                for="email"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Email Address
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                :value="user.email"
                                class="w-full rounded-lg border-gray-300 px-4 py-3 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                :class="{ 'border-red-500': errors.email }"
                                placeholder="Enter your email"
                            />
                            <p
                                v-if="errors.email"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ errors.email }}
                            </p>

                            <div
                                v-if="
                                    mustVerifyEmail && !user.email_verified_at
                                "
                                class="mt-2"
                            >
                                <p class="text-sm text-amber-600">
                                    Your email address is unverified.
                                    <Link
                                        :href="send()"
                                        as="button"
                                        class="text-emerald-600 underline hover:text-emerald-700"
                                    >
                                        Click here to resend the verification
                                        email.
                                    </Link>
                                </p>
                                <div
                                    v-if="status === 'verification-link-sent'"
                                    class="mt-2 text-sm font-medium text-green-600"
                                >
                                    A new verification link has been sent to
                                    your email address.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div
                        class="flex justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4"
                    >
                        <button
                            type="submit"
                            :disabled="processing"
                            class="flex items-center gap-2 rounded-lg bg-emerald-600 px-6 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <SpinnerIcon v-if="processing" class="h-4 w-4" />
                            <span>{{
                                processing ? 'Saving...' : 'Update Profile'
                            }}</span>
                        </button>
                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <span
                                v-show="recentlySuccessful"
                                class="self-center text-sm text-green-600"
                                >Saved!</span
                            >
                        </Transition>
                    </div>
                </div>
            </Form>

            <!-- Change Password Form -->
            <Form
                v-bind="AccountPasswordController.update.form()"
                :options="{ preserveScroll: true }"
                reset-on-success
                :reset-on-error="[
                    'password',
                    'password_confirmation',
                    'current_password',
                ]"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <div
                    class="mb-6 overflow-hidden rounded-lg border border-white bg-white/50 shadow-sm shadow-gray-50"
                >
                    <div
                        class="border-b border-gray-100 bg-linear-to-r from-amber-50 to-orange-50 px-6 py-4"
                    >
                        <div class="flex items-center gap-3">
                            <div class="rounded-lg bg-amber-100 p-2">
                                <svg
                                    class="h-5 w-5 text-amber-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">
                                    Change Password
                                </h3>
                                <p class="text-sm text-gray-500">
                                    Ensure your account is using a long, random
                                    password to stay secure
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6 p-6">
                        <!-- Current Password -->
                        <div>
                            <label
                                for="current_password"
                                class="mb-2 block text-sm font-medium text-gray-700"
                            >
                                Current Password
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="current_password"
                                name="current_password"
                                type="password"
                                class="w-full rounded-lg border-gray-300 px-4 py-3 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                :class="{
                                    'border-red-500': errors.current_password,
                                }"
                                placeholder="Enter current password"
                                autocomplete="current-password"
                            />
                            <p
                                v-if="errors.current_password"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ errors.current_password }}
                            </p>
                        </div>

                        <!-- New Password Grid -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- New Password -->
                            <div>
                                <label
                                    for="password"
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    New Password
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    class="w-full rounded-lg border-gray-300 px-4 py-3 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                    :class="{
                                        'border-red-500': errors.password,
                                    }"
                                    placeholder="Enter new password"
                                    autocomplete="new-password"
                                />
                                <p
                                    v-if="errors.password"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ errors.password }}
                                </p>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label
                                    for="password_confirmation"
                                    class="mb-2 block text-sm font-medium text-gray-700"
                                >
                                    Confirm Password
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    class="w-full rounded-lg border-gray-300 px-4 py-3 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="Confirm new password"
                                    autocomplete="new-password"
                                />
                            </div>
                        </div>

                        <p class="text-xs text-gray-500">
                            <span class="font-medium">Tip:</span> Use a
                            combination of letters, numbers, and special
                            characters for a strong password.
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div
                        class="flex justify-end gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4"
                    >
                        <button
                            type="submit"
                            :disabled="processing"
                            class="flex items-center gap-2 rounded-lg bg-amber-600 px-6 py-2 text-sm font-medium text-white transition-colors hover:bg-amber-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <SpinnerIcon v-if="processing" class="h-4 w-4" />
                            <span>{{
                                processing ? 'Changing...' : 'Change Password'
                            }}</span>
                        </button>
                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <span
                                v-show="recentlySuccessful"
                                class="self-center text-sm text-green-600"
                                >Saved!</span
                            >
                        </Transition>
                    </div>
                </div>
            </Form>
        </div>
    </AppLayout>
</template>
