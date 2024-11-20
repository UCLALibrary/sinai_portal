<template>
    <Head :title="title" />
    <div class="relative min-h-screen flex flex-col">
        <header class="border-b">
            <div class="wrap">
                <div class="flex justify-end">
                    <nav class="flex gap-4 py-2 font-dosis font-medium uppercase tracking-wide text-sm lg:text-base border-0 hover:text-sinai-red">
                        <Link
                            v-if="$page.props.auth.user && pageProps.roles.permissions.includes('view cms')"
                            :href="route('cms')"
                            title="Log in">
                            Dashboard
                        </Link>

                      <Link
                          v-if="$page.props.auth.user"
                          :href="route('logout')"
                          method="post"
                          class="font-dosis font-medium uppercase"
                          as="button"
                      >
                        Log Out
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
                <div class="main flex flex-col lg:flex-row items-center gap-y-6 pb-6 justify-between">
                    <Link
                        :href="route('home')"
                        class="logo flex">
                        <img src="/sinai-logo.svg" alt="logo" class="mr-4">Sinai Manuscripts Data Portal
                    </Link>

                    <nav class="flex flex-col lg:flex-row items-center gap-x-10 text-lg">
                        <div class="flex gap-x-4">
                            <!-- Manuscripts Dropdown -->
                            <div class="relative" v-click-outside="() => closeDropdown('manuscripts')">
                                <button @click="toggleDropdown('manuscripts')" class="flex items-center focus:outline-none uppercase font-dosis font-medium">
                                    Manuscripts
                                    <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M5.293 7.293a1 1 0 0 1 1.414 0L10 10.586l3.293-3.293a1 1 0 1 1 1.414 1.414L10 13.414l-4.707-4.707a1 1 0 0 1 0-1.414z"
                                              clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <div v-if="dropdownOpen.manuscripts" class="dropdown-menu right-0">
                                    <Link
                                        :href="route('frontend.manuscripts.index')"
                                        :class="{ 'active': route().current('frontend.manuscripts.*') }"
                                        class="dropdown-item">
                                        Manuscript Objects
                                    </Link>
                                    <Link
                                        :href="route('frontend.layers.index')"
                                        :class="{ 'active': route().current('frontend.layers.*') }"
                                        class="dropdown-item">
                                        Inscribed Layers
                                    </Link>
                                    <Link
                                        :href="route('frontend.textunits.index')"
                                        :class="{ 'active': route().current('frontend.textunits.*') }"
                                        class="dropdown-item">
                                        Text Units
                                    </Link>
                                </div>
                            </div>

                            <!-- Entities Dropdown -->
                            <div class="relative" v-click-outside="() => closeDropdown('entities')">
                                <button @click="toggleDropdown('entities')" class="flex items-center focus:outline-none uppercase font-dosis font-medium">
                                    Entities
                                    <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M5.293 7.293a1 1 0 0 1 1.414 0L10 10.586l3.293-3.293a1 1 0 1 1 1.414 1.414L10 13.414l-4.707-4.707a1 1 0 0 1 0-1.414z"
                                              clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <div v-if="dropdownOpen.entities" class="dropdown-menu right-0">
                                    <Link
                                        :href="route('frontend.agents.index')"
                                        :class="{ 'active': route().current('frontend.agents.*') }"
                                        class="dropdown-item">
                                        Agents
                                    </Link>
                                    <Link
                                        :href="route('frontend.works.index')"
                                        :class="{ 'active': route().current('frontend.works.*') }"
                                        class="dropdown-item">
                                        Works
                                    </Link>
                                    <Link
                                        :href="route('frontend.places.index')"
                                        :class="{ 'active': route().current('frontend.places.*') }"
                                        class="dropdown-item">
                                        Places
                                    </Link>
                                </div>
                            </div>

                            <!-- About Dropdown -->
                            <div class="relative" v-click-outside="() => closeDropdown('about')">
                                <button @click="toggleDropdown('about')" class="flex items-center focus:outline-none uppercase font-dosis font-medium">
                                    About
                                    <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M5.293 7.293a1 1 0 0 1 1.414 0L10 10.586l3.293-3.293a1 1 0 1 1 1.414 1.414L10 13.414l-4.707-4.707a1 1 0 0 1 0-1.414z"
                                              clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <div v-if="dropdownOpen.about" class="dropdown-menu right-0">
                                    <Link
                                        :href="route('frontend.about')"
                                        :class="{ 'active': route().current('frontend.about') }"
                                        class="dropdown-item">
                                        About the Portal
                                    </Link>
                                    <Link
                                        :href="route('frontend.datamodel')"
                                        :class="{ 'active': route().current('frontend.datamodel') }"
                                        class="dropdown-item">
                                        The Data Model
                                    </Link>
                                </div>
                            </div>
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
                    <img src="/img/logo-uclalib-wht.svg" alt="UCLA Library logo" class="h-10">
                </div>
                <div class="w-full block text-center sm:w-96">© 2024 Sinai Manuscripts Data Portal. All rights reserved.</div>
            </div>
        </footer>
    </div>
</template>

<script setup>
    import { Head, Link, usePage } from '@inertiajs/vue3'
    import { reactive, ref } from 'vue'

    // Define props
    defineProps({
        title: {
            type: String,
            required: false,
            default: 'Sinai Manuscripts Data Portal',
        },
    })

    // Get page props
    const { props: pageProps } = usePage()

    // Reactive state for dropdown menus
    const dropdownOpen = reactive({
        manuscripts: false,
        entities: false,
        about: false,
    })

    // Functions to toggle and close dropdowns
    function toggleDropdown(menu) {
        dropdownOpen[menu] = !dropdownOpen[menu]
    }

    function closeDropdown(menu) {
        dropdownOpen[menu] = false
    }

    // Custom v-click-outside directive
    const clickOutside = {
        beforeMount(el, binding) {
            el.clickOutsideEvent = (event) => {
                if (!(el === event.target || el.contains(event.target))) {
                    binding.value(event)
                }
            }
            document.body.addEventListener('click', el.clickOutsideEvent)
        },
        unmounted(el) {
            document.body.removeEventListener('click', el.clickOutsideEvent)
        },
    }

    defineExpose({
        directives: {
            clickOutside,
        },
    })
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

  .main nav a {
      &.active {
          @apply border-sinai-red
      }
      @apply font-dosis font-medium text-base md:text-lg tracking-wide underline-offset-4 border-b-2 border-b-transparent hover:border-black
  }

  .dropdown-menu {
      @apply absolute mt-2 w-48 bg-white border rounded shadow-lg z-50
  }

  .dropdown-item {
      @apply block px-4 py-2 text-black hover:bg-gray-100 font-dosis font-medium
  }
</style>
