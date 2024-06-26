<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
    <Head title="Welcome" />
    <div class="bg-sinai-beige text-gray-800">
        <div class="relative min-h-screen flex flex-col items-center justify-start">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:justify-center lg:col-start-2">
                        
                    </div>
                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('cms')"
                            class="font-dosis rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-sinai-red dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            SMDP Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="font-dosis rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-sinai-red dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Log in
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="font-dosis rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-sinai-red dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Register
                            </Link>
                        </template>
                    </nav>
                </header>

                <main class="mt-6">
                    <div class="font-dosis flex text-center justify-center mx-auto text-4xl">
                       Sinai Manuscripts Data Portal
                    </div>
                    <div class="flex justify-center mt-16 mb-16">
                        <img src="/sinai-logo.svg" alt="Logo" class="w-32 h-32" />
                    </div>
                </main>

                <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                    © 2024 Sinai Manuscripts Data Portal. All rights reserved.
                </footer>
            </div>
        </div>
    </div>
</template>
