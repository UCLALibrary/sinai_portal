import './bootstrap'
import '../css/app.css'
import '@jsonforms/vue-vanilla/vanilla.css'
import '@mdi/font/css/materialdesignicons.css'
import 'floating-vue/dist/style.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'

// mitt
import mitt from 'mitt'
const emitter = mitt()

import VueObserveVisibility from 'vue3-observe-visibility'

// vuetify
import vuetify from './vuetify'

// element-plus
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import '../css/overrides/element-plus.css'

import '../css/record-detail-view.css'

// element-plus icons
import * as ElementPlusIconsVue from '@element-plus/icons-vue'

const appName = import.meta.env.VITE_APP_NAME || 'Sinai Manuscripts Data Portal'

createInertiaApp({
    title: (title) => `${title} | ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })

        // emitter
        app.config.globalProperties.emitter = emitter

        // element-plus icons
        for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
            app.component(key, component)
        }

        return app
            .use(plugin)
            .use(ZiggyVue)
            .use(VueObserveVisibility)
            .use(vuetify)
            .use(ElementPlus)
            .mount(el)
    },
    progress: {
        color: '#4B5563',
    },
})
