<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <div>
        <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
            <ApplicationLogo class="block h-12 w-auto" />

            <h1 class="mt-8 text-2xl font-medium text-gray-900">
                Welcome to the Sinai Manuscripts Data Portal CMS
            </h1>

            <p class="mt-6 text-gray-500 leading-relaxed">
                As the new centralized data store for the Sinai Manuscripts Digital Library website, the Portal will house records for all Sinai-related data. The database will store all cataloguing and descriptive data collected, as well as any future descriptive work. Project scholars and cataloguers will be able to submit new descriptive metadata through the application’s administrative interface.
            </p>
        </div>

        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-14 p-6 lg:p-8">
            <div>
                <div class="flex items-center">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <a href="/cms/manuscripts">Manuscripts</a>
                    </h2>
                </div>

                <p class="mt-2 text-gray-500 text-sm leading-relaxed">
                    Create and edit all manuscripts in the database. Manuscripts are the highest level of description in the database and are the parent records for all other descriptive data.
                </p>

                <p class="mt-2 text-sm">
                    <a href="/cms/manuscripts" class="inline-flex items-center font-semibold text-sinai-red">
                        Manage Manuscripts

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 w-5 h-5 fill-sinai-red">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>

            <div>
                <div class="flex items-center">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <a href="/cms/codicological-units">Codicological Units</a>
                    </h2>
                </div>

                <p class="mt-2 text-gray-500 text-sm leading-relaxed">
                    Create and edit all codicological units in the database. Codicological units are the child records of manuscript objects and represent the physical parts of a manuscript, such as folios, quires, and bindings.
                </p>

                <p class="mt-2 text-sm">
                    <a href="/cms/codicological-units" class="inline-flex items-center font-semibold text-sinai-red">
                        Manage Codicological Units

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 w-5 h-5 fill-sinai-red">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>

            <div>
                <div class="flex items-center">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <a href="'/cms/textunits">Text Units</a>
                    </h2>
                </div>

                <p class="mt-2 text-gray-500 text-sm leading-relaxed">
                    Manage Text Units that can be attached to other records in the database.
                </p>

                <p class="mt-2 text-sm">
                    <a href="/cms/textunits" class="inline-flex items-center font-semibold text-sinai-red">
                        Manage Text Units

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 w-5 h-5 fill-sinai-red">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>

            <div>
                <div class="flex items-center">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <a href="/cms/agents">Agents</a>
                    </h2>
                </div>

                <p class="mt-2 text-gray-500 text-sm leading-relaxed">
                    Manage Agents (persons and institutions) that can be attached to other records in the database.
                </p>

                <p class="mt-2 text-sm">
                    <a href="/cms/agents" class="inline-flex items-center font-semibold text-sinai-red">
                        Manage Agents

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 w-5 h-5 fill-sinai-red">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>

            <div>
                <div class="flex items-center">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <a href="'/cms/places">Places</a>
                    </h2>
                </div>

                <p class="mt-2 text-gray-500 text-sm leading-relaxed">
                    Manage Places that can be attached to other records in the database.
                </p>

                <p class="mt-2 text-sm">
                    <a href="/cms/places" class="inline-flex items-center font-semibold text-sinai-red">
                        Manage Places

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 w-5 h-5 fill-sinai-red">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>

            <div>
                <div class="flex items-center">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <a href="'/cms/bibliography">Bibliography</a>
                    </h2>
                </div>

                <p class="mt-2 text-gray-500 text-sm leading-relaxed">
                    Manage Bibliography that can be attached to other records in the database.
                </p>

                <p class="mt-2 text-sm">
                    <a href="/cms/bibliography" class="inline-flex items-center font-semibold text-sinai-red">
                        Manage Bibliography

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 w-5 h-5 fill-sinai-red">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>

            <div>
                <div class="flex items-center">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <a href="/cms/users">Users</a>
                    </h2>
                </div>

                <p class="mt-2 text-gray-500 text-sm leading-relaxed">
                    Manage all aspects of user accounts in the database. Users are the people who will be using the application to create and edit records in the database.
                </p>

                <p class="mt-2 text-sm">
                    <a href="/cms/users" class="inline-flex items-center font-semibold text-sinai-red">
                        Manage Users

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 w-5 h-5 fill-sinai-red">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>

            <div>
                <div class="flex items-center">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <a href="/cms/languages">Vocabulary</a>
                    </h2>
                </div>

                <p class="mt-2 text-sm">
                    <a href="/cms/languages" class="inline-flex items-center font-semibold text-sinai-red">
                        Manage Languages

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 w-5 h-5 fill-sinai-red">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>

                 <p class="mt-2 text-sm">
                    <a href="/cms/references" class="inline-flex items-center font-semibold text-sinai-red">
                        Manage References

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 w-5 h-5 fill-sinai-red">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>

                  <p class="mt-2 text-sm">
                    <a href="/cms/features" class="inline-flex items-center font-semibold text-sinai-red">
                        Manage Features

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 w-5 h-5 fill-sinai-red">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>

                  <p class="mt-2 text-sm">
                    <a href="/cms/locations" class="inline-flex items-center font-semibold text-sinai-red">
                        Manage Locations

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 w-5 h-5 fill-sinai-red">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>

                  <p class="mt-2 text-sm">
                    <a href="/cms/scripts" class="inline-flex items-center font-semibold text-sinai-red">
                        Manage Scripts

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 w-5 h-5 fill-sinai-red">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>
        </div>
    </div>
</template>
