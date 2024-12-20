<script setup>
    import FrontendLayout from '@/Layouts/FrontendLayout.vue';
</script>

<template>
    <FrontendLayout title="Sinai Manuscripts Data Portal">
        
        <div class="m-16">
            <div class="dark:text-white p-6">
                <h2 class="font-dosis text-2xl uppercase">About</h2>
                <p class="mt-4 text-lg">
                    <p>
                        The Sinai Data Portal is an open-access database for managing and sharing the manuscript descriptions and related authority files for the <a class="text-sinai-dark-blue" href="https://sinaimanuscripts.library.ucla.edu/">Sinai Manuscripts Digital Library</a> (SMDL). Funded through an NEH Digital Humanities Advancement Grant, the Portal was developed to:</p>
                        
                        <ul class="indent my-8">
                            <li class="mb-2">Serve as a datastore for all metadata and descriptions related to the Sinai manuscripts</li>
                            <li class="mb-2">Facilitate participation from expert scholars who wish to contribute to the manuscript descriptions</li>
                            <li class="mb-2">Provide open and accessible structured data for use in research and teaching</li>
                            <li class="mb-2">Support and encourage data sharing with peer projects and institutions</li>
                        </ul>
                    <p>
                        Foundational to the Portal is the new data model, designed to better represent the complexities inherent to early manuscripts. The model takes a modular approach which arranges the descriptions into discrete Manuscript Object, Inscribed Layer, and Text Unit records. For more information on the model, see <a class="text-sinai-dark-blue" href="/datamodel">The Data Model</a> page.
                    </p>

                    <p>All data are downloadable as JSON documents, either for a single record from the record’s item page, or if logged in, as a bulk download from one of the search index pages. The data are also available for download via our <a class="text-sinai-dark-blue" href="https://github.com/UCLALibrary/sinaiportal_data">GitHub repository</a>.</p>
                    
                    <h3>Manuscript Descriptions</h3>
                    <p>For the majority of manuscripts, descriptions are derived from various print catalogues, relying heavily on Murad Kamil’s Catalogue of all Manuscripts in the Monastery of St. Catharine (1970), Philothée du Sinaï's Nouveaux manuscrits syriaques du Sinaï (2008), and Yiannis Meimaris' Katalogos ton neon aravikon cheirographon tes Hieras Mones Hagias Aikaterines tou Orous Sinai (1985). The descriptions were compiled as part of the Sinai Library Digitization Project (SLDP) (2018-2022), during which they were enhanced to facilitate search and discovery.</p>

                    <p>Most records are relatively sparse, but the Sinai work is ongoing and descriptions will evolve over time. Concurrent with the Portal project, we are working on another NEH-funded project, Identification and Description of the Syriac and Arabic Manuscripts at St. Catherine’s Monastery of the Sinai. Phase 1: Syriac Parchment Manuscripts. Contributing scholars include Grigory Kessel and Natalia Smelova, who are describing the Syriac parchment manuscripts, and Vevian Zaki, who identified and described the Arabic core and paratextual contents in Syriac manuscripts. The resulting description will be incorporated into the Portal during the course of the project.</p>

                    <p>Metadata for the undertext objects was compiled during the Sinai Palimpsests Project (SPP) (2011-2016) under the direction of Claudia Rapp (University of Vienna), in partnership with the Early Manuscripts Electronic Library (EMEL) and the UCLA Library, and funded by the Arcadia Fund. For a list of contributing scholars, see the <a class="text-sinai-dark-blue" href="https://sinai.library.ucla.edu/terms-of-use/contributors">SPP Contributors</a> page. Contributors are credited in Portal records.</p>

                    Have questions or want to contribute? Email us at <a class="text-sinai-dark-blue" href="mailto:sinai@library.ucla.edu">sinai@library.ucla.edu</a>.
                    
                    <h3>Contributors</h3>
                    
                    <p>
                        <b>Project Team:</b>
                        Dawn Childress, Director, Sinai Manuscripts Digital Library and Portal, UCLA Library
                        William Potter, Project and Data Manager, UCLA Library
                    </p>

                    <p>
                        <b>Research Associates:</b> <br>
                        Justin Arnwine (2022-2024) <br>
                        Maroun El Houkayem (2022-2024) <br>
                        Chris PreJean (2022-2024) <br>
                        Michael Wingert (2022-2024) <br>
                    </p>

                    <p>
                        <b>Graduate Assistants:</b> <br>
                        Dealla Goodings (2023- ) <br>
                        Krystal Navarro (2024- ) <br>
                        Ngozi Harrison (2024) <br>
                        Alexis Roberto (2023-2024) <br>
                    </p>
                    
                    <p>
                        <b>Advisory Council</b> <br>
                        Sebastian Brock, University of Oxford, emeritus <br>
                        Grigory Kessel, Austrian Academy of Arts and Sciences <br>
                        Jeanne-Nicole Mellon Saint-Laurent, Marquette University <br>
                        David Michelson, Vanderbilt University <br>
                        Rob Sanderson, Yale University <br>
                        Jack Tannous, Princeton University <br>
                    </p>
                    
                    <p>
                        <b>Web and Database Development</b> <br>
                        text & bytes, <a class="text-sinai-dark-blue" href="https://www.textandbytes.com">https://www.textandbytes.com</a>
                    </p>


                </p>
            </div>
        </div>
    </FrontendLayout>
</template>

<style scoped>
    p, ul {
        @apply max-w-[70rem] text-lg
    }

    p {
        @apply mb-4
    }

    h3 {
        @apply font-semibold text-xl mt-4 mb-2
    }
</style>