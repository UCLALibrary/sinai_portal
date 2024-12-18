<script setup>
    import FrontendLayout from '@/Layouts/FrontendLayout.vue';
</script>

<template>
    <FrontendLayout title="Sinai Manuscripts Data Portal">
        
        <div class="mx-16 flex justify-center">
            <div class="dark:text-white p-6">
                <h2 class="font-dosis text-2xl uppercase">Data Model</h2>
                <p class="mt-4">
                    The new data model is at the heart of the Sinai Manuscripts Data Portal (SMDP). The model was designed to accommodate the numerous material complexities in early manuscripts, especially those related to composite manuscripts and disbinding/rebinding, those with multiple textual units, disjecta membra, and the undertext layers in palimpsests. The SMDP model addresses these complexities by defining three distinct yet interconnected concepts that target specific aspects of a manuscript:
                </p>

                <ul class="indent my-8">
                    <li class="mb-4">
                        A <b>Manuscript Object</b> represents a physical or virtual “container” of sorts that has a structure (binding, collation, parts, substrate) and its own history and provenance. The Manuscript Object schema defines the properties and attributes for documenting and describing these structural, contextual, and historical aspects. A Manuscript Object can be an extant manuscript codex or fragment, a representation of an earlier codex that carried what is now an undertext layer in an extant manuscript, or a digital reconstruction (or partial reconstruction) of an earlier codex where folios and fragments have been dispersed. Manuscript Objects link to all of the Inscribed Layers that are present in a manuscript object.
                    </li>
    
                    <li class="mb-4">
                        An <b>Inscribed Layer</b> represents a single material layer of writing, illustration, decoration, or other markings that conveys the intellectual and creative content of a scribal production unit. The Inscribed Layer schema defines the properties and attributes for documenting and describing the codicological features of each layer, such as writing and hands, inks, page layout, and ornamentation; as well as history and provenance data related to the layer, such as colophons, waqfs, and other contextual notes. An Inscribed Layer can be the primary overtext layer of its parent Manuscript Object, an undertext layer, or a guest content layer added post-production or through other interventions like binding and repairs. Inscribed Layers link to all of the Text Units that are present in a layer.
                    </li>
    
                    <li class="mb-4">
                        A <b>Text Unit</b> represents a textual artifact, comprising one or more work witnesses, that is intended to be encountered as a singular composition. The Text Unit schema defines the properties and attributes for documenting and describing work witnesses, incipits and explicits, attested creators or contributors, paracontent such as rubrics and subscriptions, and other intellectual content that accompanies the text.
                    </li>
                </ul>

                <p>
                    The model also includes concepts for related entities present in the manuscripts, which serve as authority files for Works (the distinct intellectual or creative composition), Agents (people and organizations related to or attested in manuscripts), and Places (attested locations, regions, settlements, etc.).
                </p>

                <p>
                    The model treats each of these concepts—manuscript or entity—as distinct records within the Portal while preserving their relationships through unique IDs and relationship taxonomies referenced within the records.
                </p>

                <p>
                    Visit our <a href="https://github.com/UCLALibrary/sinaiportal_data" target="_blank" class="text-sinai-dark-blue">GitHub</a> repository to find more information on the data model, including JSON Schemas, model diagrams, and a dictionary of JSON properties.
                </p>
            </div>
        </div>
    </FrontendLayout>
</template>

<style scoped>
    p, ul {
        @apply max-w-[70rem] text-lg
    }
</style>
