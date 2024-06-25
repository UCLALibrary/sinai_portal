<template>
    <div class="min-h-screen flex flex-col sm:justify-start pt-24 items-center bg-sinai-beige">
        <div class="text-xl mb-8">Sinai Manuscripts Data Portal</div>
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>
