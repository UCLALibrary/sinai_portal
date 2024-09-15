<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        required: false,
        default: 'Sinai Manuscripts Data Portal',
    },
});
</script>

<template>
    <Head :title="title" />
    <div class="bg-sinai-beige text-gray-800">
        <div class="relative flex flex-col items-center justify-start">
            <div class="relative h-screen flex flex-col w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="items-center gap-2 py-10 lg:grid-cols-3">
                    <nav class="-mx-3 flex flex-1 justify-between">
                        <div class="">
                          <Link
                              :href="route('home')"
                              class="font-dosis rounded-md px-3 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-sinai-red dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                              Home
                          </Link>
                          <Link
                              :href="route('frontend.agents.index')"
                              class="font-dosis rounded-md px-3 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-sinai-red dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                              Agents
                          </Link>
                        </div>
                          <div>
                            <Link
                                v-if="$page.props.auth.user"
                                :href="route('cms')"
                                class="font-dosis rounded-md px-3 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-sinai-red dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                                SMDP Dashboard
                            </Link>

                            <template v-if="!$page.props.auth.user">
                              <Link
                                  
                                  :href="route('login')"
                                  class="font-dosis rounded-md px-3 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-sinai-red dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                                  Log in
                              </Link>

                              <Link
                                  :href="route('register')"
                                  class="font-dosis rounded-md px-3 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-sinai-red dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                                  Register
                              </Link>
                          </template>
                        </div>
                        
                    </nav>
                </header>

                <main class="mt-2">
                    <slot />
                </main>

                <footer class="mt-auto py-16 text-center text-sm text-black dark:text-white/70">
                    © 2024 Sinai Manuscripts Data Portal. All rights reserved.
                </footer>
            </div>
        </div>
    </div>
</template>
