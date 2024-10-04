<template>
    <div class="min-h-screen flex flex-col sm:justify-start pt-24 items-center">
        <div class="logo">
            <img src="/sinai-logo.svg" alt="logo">Sinai Manuscripts Data Portal
        </div>

        <div class="w-full sm:max-w-lg mt-8 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded">
            <slot />
        </div>
    </div>
</template>

<style lang="postcss" scoped>
    .logo {
        img {
            @apply h-12 w-12 xl:h-14 xl:w-14 mr-2 lg:mr-3 xl:mr-4
        }
        @apply flex items-center font-dosis font-medium text-xl lg:text-2xl xl:text-3xl text-black uppercase
    }
</style>
