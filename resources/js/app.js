require('./bootstrap')

import Vue from 'vue'
import VueApollo from 'vue-apollo'

import { InertiaApp } from '@inertiajs/inertia-vue'
import { InertiaForm } from 'laravel-jetstream'
import PortalVue from 'portal-vue'
import { apolloClient } from './ApolloClient'

Vue.use(VueApollo)

// eslint-disable-next-line no-undef
Vue.mixin({ methods: { route } })
Vue.use(InertiaApp)
Vue.use(InertiaForm)
Vue.use(PortalVue)

const apolloProvider = new VueApollo({
    defaultClient: apolloClient
})

const app = document.getElementById('app')

Vue.component('Toast', () => import('./Components/Toast.vue'))

new Vue({
    apolloProvider,
    render: h =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: name => require(`./Pages/${name}`).default
            }
        })
}).$mount(app)
