import { defineAsyncComponent } from 'vue'

Vue.component('reorder', defineAsyncComponent(() => import('./components/Reorder.vue')))
