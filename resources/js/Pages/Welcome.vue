<script setup>
    import FrontendLayout from '@/Layouts/FrontendLayout.vue';
</script>

<template>
    <FrontendLayout title="Sinai Manuscripts Data Portal">
        
        <div class="flex justify-center text-center my-4 mx-16">
            <p class="home-intro-text">
                Welcome to the Sinai Manuscripts Data Portal! The Portal provides access to the descriptions and related authority files for the manuscripts of St. Catherine’s Monastery in the Sinai. Manuscript descriptions are arranged into discrete conceptual records for Text Units, Inscribed Layers, and Manuscript Objects for each manuscript. For more information on this, see <a :href="route('frontend.datamodel')" class="text-sinai-dark-blue">The Data Model</a> page. As an evolving project, description and development are ongoing.
            </p>
        </div>

        <div class="m-auto w-[24rem] h-[0.1rem] bg-sinai-red"></div>

        <div class="home-boxes my-8 mx-16  h-full">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <a :href="route('frontend.manuscripts.index')" class="block">
                    <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6 flex flex-col h-full 
                                transform transition-transform duration-300 hover:scale-105 cursor-pointer">
                        <h2 class="font-dosis text-2xl">Manuscript Objects</h2>
                        <p class="mt-4 text-base">
                            Browse descriptions of the structural features of manuscripts (collation, parts, supports, etc.) and its history and provenance.
                        </p>
                    </div>
                </a>
                    <a :href="route('frontend.layers.index')" class="block">
                    <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6 flex flex-col h-full 
                                transform transition-transform duration-300 hover:scale-105 cursor-pointer">
                        <h2 class="font-dosis text-2xl">Inscribed Layers</h2>
                        <p class="mt-4 text-base">
                            Browse descriptions of the codicological features of core overtext and guest content layers.
                        </p>
                    </div>
                </a>
                    <a :href="route('frontend.textunits.index')" class="block">
                    <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6 flex flex-col h-full 
                                transform transition-transform duration-300 hover:scale-105 cursor-pointer">
                        <h2 class="font-dosis text-2xl">Text Units</h2>
                        <p class="mt-4 text-base">
                            Browse descriptions of the textual features and intellectual contents of manuscripts.
                        </p>
                    </div>
                </a>
                <a :href='route("frontend.layers.index", { filters: "[\"state:Undertext\"]" })' class="block">
                    <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6 flex flex-col h-full 
                                transform transition-transform duration-300 hover:scale-105 cursor-pointer">
                        <h2 class="font-dosis text-2xl">Undertext Layers</h2>
                        <p class="mt-4 text-base">
                            Browse all Undertext Inscribed Layers from palimpsested manuscripts.
                        </p>
                    </div>
                </a>
                <div class="block">
                    <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6 flex flex-col h-full 
                                transform transition-transform duration-300 hover:scale-105 cursor-pointer">
                        <h2 class="font-dosis text-2xl">Digital Rebindings</h2>
                        <p class="mt-4 text-base">
                            Forthcoming
                        </p>
                    </div>
                </div>
                <div class="block">
                    <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6 flex flex-col h-full 
                                transform transition-transform duration-300 hover:scale-105 cursor-pointer">
                        <h2 class="font-dosis text-2xl">Guest Layers</h2>
                        <p class="mt-4 text-base">
                            Forthcoming
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <a :href="route('frontend.works.index')" class="block">
                    <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6 flex flex-col h-full 
                                transform transition-transform duration-300 hover:scale-105 cursor-pointer">
                        <h2 class="font-dosis text-2xl">Works</h2>
                        <p class="mt-4 text-base">
                            Browse authority records for works attested in the Sinai manuscripts.
                        </p>
                    </div>
                </a>
                <a :href="route('frontend.agents.index')" class="block">
                    <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6 flex flex-col h-full 
                                transform transition-transform duration-300 hover:scale-105 cursor-pointer">
                        <h2 class="font-dosis text-2xl">Agents</h2>
                        <p class="mt-4 text-base">
                            Browse authority records for people and organizations mentioned in or related to the Sinai manuscripts, including attested authors, scribes, owners, and more.
                        </p>
                    </div>
                </a>
            </div>
        </div>


    </FrontendLayout>
</template>

<style scoped>
    .home-intro-text {
        font-size: 1.3rem;
        line-height: 1.7;
        max-width: 70rem;
    }

    .home-boxes {
        h2 {
            @apply uppercase text-sinai-dark-blue
        }
    }
</style>