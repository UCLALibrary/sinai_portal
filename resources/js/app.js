import './bootstrap'
import '../css/app.css'
import '../css/jsonforms.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'

// mitt
import mitt from 'mitt'
const emitter = mitt()

// vuetify
import vuetify from './vuetify'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
    title: (title) => `${title} | ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
        app.config.globalProperties.emitter = emitter
        return app
            .use(plugin)
            .use(ZiggyVue)
            .use(vuetify)
            .mount(el)
    },
    progress: {
        color: '#4B5563',
    },
})
