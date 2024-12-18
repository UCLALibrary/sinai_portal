<script setup>
    import FrontendLayout from '@/Layouts/FrontendLayout.vue';
</script>

<template>
    <FrontendLayout title="Sinai Manuscripts Data Portal">
        
        <div class="flex justify-center mt-16 mb-16">
            <p class="text-center leading-loose">
                Welcome to the Sinai Manuscripts Data Portal! The Portal provides access to the descriptions and related authority files for the manuscripts of St. Catherine’s Monastery in the Sinai. Manuscript descriptions are arranged into discrete conceptual records for Text Units, Inscribed Layers, and Manuscript Objects for each manuscript. For more information on this, see <a :href="route('frontend.datamodel')" class="text-sinai-dark-blue">The Data Model</a> page. As an evolving project, description and development are ongoing.
            </p>
        </div>
        <div class="m-16 h-full">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <a :href="route('frontend.manuscripts.index')" class="block">
                    <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6 flex flex-col h-full 
                                transform transition-transform duration-300 hover:scale-105 cursor-pointer">
                        <h2 class="font-dosis text-2xl">Manuscript Objects</h2>
                        <p class="mt-4 text-lg">
                            Browse descriptions of the structural features of manuscripts, such as collation, parts of composite manuscripts, and writing supports; its history and provenance; and references/links to a manuscript’s Inscribed Layer records.
                        </p>
                    </div>
                </a>
                    <a :href="route('frontend.layers.index')" class="block">
                    <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6 flex flex-col h-full 
                                transform transition-transform duration-300 hover:scale-105 cursor-pointer">
                        <h2 class="font-dosis text-2xl">Inscribed Layers</h2>
                        <p class="mt-4 text-lg">
                            Browse descriptions of the codicological features of inscribed layers, such as writing and hands, ink, page layout, colophons, related paracontent, and links to a layer’s Text Unit records. Layer types include core overtext, palimpsest’s undertext, and guest content.
                        </p>
                    </div>
                </a>
                    <a :href="route('frontend.textunits.index')" class="block">
                    <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6 flex flex-col h-full 
                                transform transition-transform duration-300 hover:scale-105 cursor-pointer">
                        <h2 class="font-dosis text-2xl">Text Units</h2>
                        <p class="mt-4 text-lg">
                            Browse descriptions of the textual features and intellectual contents within the manuscript layers. Text units comprise one or more work witnesses and focus on identifying and describing witnesses, related paracontent, and other features of the text.
                        </p>
                    </div>
                </a>
                <a :href='route("frontend.layers.index", { filters: "[\"state:Undertext\"]" })' class="block">
                    <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6 flex flex-col h-full 
                                transform transition-transform duration-300 hover:scale-105 cursor-pointer">
                        <h2 class="font-dosis text-2xl">Undertext Layers</h2>
                        <p class="mt-4 text-lg">
                            Browse all Undertext Inscribed Layers from palimpsested manuscripts
                        </p>
                    </div>
                </a>
                <div class="block">
                    <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6 flex flex-col h-full 
                                transform transition-transform duration-300 hover:scale-105 cursor-pointer">
                        <h2 class="font-dosis text-2xl">Digital Rebindings</h2>
                        <p class="mt-4 text-lg">
                            Forthcoming
                        </p>
                    </div>
                </div>
                <div class="block">
                    <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6 flex flex-col h-full 
                                transform transition-transform duration-300 hover:scale-105 cursor-pointer">
                        <h2 class="font-dosis text-2xl">Guest Layers</h2>
                        <p class="mt-4 text-lg">
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
                        <p class="mt-4 text-lg">
                            Browse authority records for works attested in the Sinai manuscripts.
                        </p>
                    </div>
                </a>
                <a :href="route('frontend.agents.index')" class="block">
                    <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6 flex flex-col h-full 
                                transform transition-transform duration-300 hover:scale-105 cursor-pointer">
                        <h2 class="font-dosis text-2xl">Agents</h2>
                        <p class="mt-4 text-lg">
                            Browse authority records for people and organizations mentioned in or related to the Sinai manuscripts.
                        </p>
                    </div>
                </a>
            </div>
        </div>


    </FrontendLayout>
</template>
