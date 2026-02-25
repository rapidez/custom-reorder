import { defineAsyncComponent } from 'vue'

document.addEventListener('vue:loaded', function (event) {
    const vue = event.detail.vue
    vue.component('reorder', defineAsyncComponent(() => import('./components/Reorder.vue')))
})
