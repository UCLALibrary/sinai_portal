<script setup>
    import FrontendLayout from '@/Layouts/FrontendLayout.vue';
</script>

<template>
    <FrontendLayout title="Sinai Manuscripts Data Portal">
        
        <div class="flex justify-center mt-16 mb-16">
            <img src="/sinai-logo.svg" alt="Logo" class="w-32 h-32" />
        </div>
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 m-16">
            <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6">
                <h2 class="font-dosis text-2xl">About</h2>
                <p class="mt-4 text-lg">
                    As the new centralized data store for the Sinai Manuscripts Digital Library website, the Sinai Manuscripts Data Portal will house records for all Sinai-related data. The database will store all cataloguing and descriptive data collected, as well as any future descriptive work. Project scholars and cataloguers will be able to submit new descriptive metadata through the application’s administrative interface. 
                </p>
            </div>
            <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6">
                <h2 class="font-dosis
                text-2xl">Browse</h2>
                <p class="mt-4 text-lg">
                    The portal provides access to detailed metadata of the Sinai Manuscripts Digital Library. Users can search, browse, and download the the metadata, and contribute to the project.
                </p>
            </div>
        </div>
    </FrontendLayout>
</template>
