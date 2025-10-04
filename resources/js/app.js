import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

// 🔧 scan semua pages bertingkat (Pages/**)
const pages = import.meta.glob('./Pages/**/*.vue')

createInertiaApp({
  resolve: name => {
    const importPage = pages[`./Pages/${name}.vue`]
    if (!importPage) throw new Error(`Page not found: ${name}`)
    return importPage()
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },
})
