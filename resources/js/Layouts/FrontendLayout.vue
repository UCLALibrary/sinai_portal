<template>
    <Head :title="title" />
    <div class="relative min-h-screen flex flex-col">
        <header class="border-b">
            <div class="wrap">
                <div class="flex justify-end">
                    <nav class="service flex gap-4 py-2">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('cms')"
                            title="Log in">
                            Dashboard
                        </Link>

                    <template v-if="!$page.props.auth.user">
                        <Link
                            :href="route('register')">
                            Register
                        </Link>

                        <Link                         
                            :href="route('login')"
                            class="">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" /></svg>
                        </Link>

                    </template>
                    </nav>
                </div>
                <div class="flex flex-col lg:flex-row items-center gap-y-6 pb-10 justify-between">
                    <Link
                        :href="route('home')"
                        class="logo flex w-full">
                        <img src="/sinai-logo.svg" alt="logo" class="mr-4">Sinai Manuscripts Data Portal
                    </Link>

                    <nav class="flex flex-col lg:flex-row items-center gap-x-10 text-lg">
                        <div class="flex gap-x-4">
                            <Link
                                :href="route('frontend.agents.index')"
                                :class="{ 'active': route().current('frontend.agents.index') || route().current('frontend.agents.show') }">
                                Agents
                            </Link>
                            <Link
                                :href="route('frontend.places.index')"
                                :class="{ 'active': route().current('frontend.places.index') || route().current('frontend.places.show') }">
                                Places
                            </Link>
                            <Link
                                :href="route('frontend.works.index')"
                                :class="{ 'active': route().current('frontend.works.index') || route().current('frontend.works.show') }">
                                Works
                            </Link>
                            <Link
                                :href="route('frontend.manuscripts.index')"
                                :class="{ 'active': route().current('frontend.manuscripts.index') || route().current('frontend.manuscripts.show') }">
                                Manuscripts
                            </Link>
                            <Link
                                :href="route('frontend.about')"
                                :class="{ 'active': route().current('frontend.about') }">
                                About
                            </Link>
                        </div>
                    </nav>

                </div>
            </div>
        </header>

        <main>
            <div class="wrap">
                <slot />
            </div>
        </main>

        <footer class="bg-black mt-auto py-16 text-sm text-white">
            <div class="wrap">
                <div class="pb-12 flex flex-col sm:flex-row items-center gap-8">
                    <img src="/img/logo-sinai-wht.png" alt="St Catherine Monastery logo" class="h-16">
                    <img src="/img/logo-uclalib-wht.svg" alt="St Catherine Monastery logo" class="h-10">

                </div>
                <div class="w-full block text-center sm:w-96">© 2024 Sinai Manuscripts Data Portal. All rights reserved.</div>
            </div>
        </footer>
    </div>
</template>

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

<style lang="postcss" scoped>
    .wrap {
        @apply mx-auto w-full lg:max-w-8xl px-4 lg:px-6
    }

    .logo {
        img {
            @apply h-12 w-12 xl:h-14 xl:w-14 mr-2 lg:mr-3 xl:mr-4
        }
        @apply flex items-center font-dosis font-medium text-xl lg:text-2xl xl:text-3xl text-black uppercase
    }

    nav a {
        &.active {
            @apply border-black
        }
        @apply font-dosis uppercase font-medium text-base md:text-lg tracking-wide underline-offset-4 border-b-2 border-b-transparent hover:border-black

    }

    nav.service a {
        @apply text-sm lg:text-base border-0 hover:text-sinai-red
    }

</style>