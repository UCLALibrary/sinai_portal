<script setup>
    import FrontendLayout from '@/Layouts/FrontendLayout.vue';
</script>

<template>
    <FrontendLayout title="Sinai Manuscripts Data Portal">
        
        <div class="flex justify-center mt-16 mb-16">
            <p class="text-center">
                The Sinai Manuscripts Data Portal provides access to the structured descriptions of manuscripts from St. Catherine’s Monastery in Sinai, Egypt. The Portal presents manuscripts as physical containers that comprise assemblages of inscribed layers and texts. This modular approach results in discrete records for each Text Unit, Inscribed Layer, and Manuscript Object, while documenting the connections between these descriptive components. The Portal also serves as an authority file for the works, people, organizations, and places connected to the manuscripts. As an evolving project, many of the records contain only basic descriptions; however, description is ongoing, with updates published regularly. For more information about the Portal, description conventions, and the underlying data model, see the About pages.
            </p>
        </div>
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 m-16">
            <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6 flex flex-col">
                <h2 class="font-dosis text-2xl">Works</h2>
                <div class="flex flex-col h-full justify-between">
                    <p class="mt-4 text-lg">
                        Browse authority records for works attested in the Sinai manuscripts.
                    </p>
                   <p class="">
                        <a :href="route('frontend.works.index')" class="mt-auto text-sinai-dark-blue">
                            Works
                        </a>
                   </p>
               </div>
            </div>
            <div class="bg-white dark:bg-black dark:text-white rounded-lg shadow-lg p-6 flex flex-col">
                <h2 class="font-dosis text-2xl">Agents</h2>
                <div class="flex flex-col h-full justify-between">
                    <p class="mt-4 text-lg">
                        Browse authority records for people and organizations mentioned in or related to the Sinai manuscripts, including authors of attested works, scribes, owners, patrons, and more.
                    </p>
                    <p class="">
                        <a :href="route('frontend.agents.index')" class="mt-auto text-sinai-dark-blue">
                            Agents
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </FrontendLayout>
</template>
