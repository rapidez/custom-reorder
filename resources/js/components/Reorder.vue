<script>
import { mask as cartMask } from 'Vendor/rapidez/core/resources/js/stores/useMask'

export default {
    props: {
        items: Array,
        cartItems: {
            type: Boolean,
            default: false,
        }
    },
    render() {
        return this.$scopedSlots.default(this)
    },

    data() {
        return {
            loading: true,
            selectedItems: [],
            matchingItems: [],
            unconfiguredItems: [],
            urls: {},
            adding: false,
            added: false,
        }
    },

    mounted() {
        if (this.items) {
            this.getMatchingProducts()
        }

        this.$root.$on('reorder-all', () => {
            this.selectedItems = [...this.matchingItems]
        })
    },

    watch: {
        items() {
            this.getMatchingProducts()
        }
    },

    methods: {
        canReorder(item) {
            let filters = window.config?.custom_reorder?.sku_filters ?? []
            if (filters.some((filter) => this.transformItem(item).sku.match(new RegExp(filter)))) {
                return false
            }

            return true
        },

        async getMatchingProducts() {
            let skus = this.transformedItems.map(item => item.sku)
            let response = await window.magentoGraphQL(
                `query Products {
                    products(
                        filter: { sku: { in: [${skus.map(sku => `"${sku}"`).join(',')}] } }
                        pageSize: 999
                        currentPage: 1
                    ) {
                        items {
                            sku
                            url_rewrites { url }
                            __typename
                            ... on CustomizableProductInterface { options { required } }
                        }
                    }
                }`
            )
            let matchingItems = Object.fromEntries((response.data.products.items ?? []).map(item => [item.sku, item]))

            this.matchingItems = this.transformedItems
                .filter(item => item.sku in matchingItems)
                .filter(item => !this.isUnconfigured(item, matchingItems[item.sku]))
                .map(this.itemId)

            this.unconfiguredItems = this.transformedItems
                .filter(item => item.sku in matchingItems)
                .filter(item => this.isUnconfigured(item, matchingItems[item.sku]))
                .map(this.itemId)

            this.urls = Object.fromEntries((response.data.products.items ?? []).map(item =>
                [item.sku, item.url_rewrites?.length ? url('/' + item.url_rewrites[0].url) : null]
            ))

            this.loading = false
        },

        isUnconfigured(item, currentItem) {
            let options = (currentItem.options ?? [])
                .filter(option => option.required)

            if (!options.length && currentItem.__typename !== 'ConfigurableProduct') {
                return false
            }

            if (item && (item.entered_options || item.selected_options)) {
                return false
            }

            return true
        },

        async addToCart() {
            if (!this.selectedItems.length) {
                return
            }
            this.adding = true

            try {
                let response = await window.magentoGraphQL(
                    `mutation ($cartId: String!, $cartItems: [CartItemInput!]!) {
                        addProductsToCart(cartId: $cartId, cartItems: $cartItems)
                        { cart { ...cart } user_errors { code message } }
                    }
                    ` + config.fragments.cart,
                    {
                        cartId: cartMask.value,
                        cartItems: this.selectedItems.map((id) => {
                            let item = this.transformedItems.find((item, index) => this.itemId(item, index) == id)
                            return item
                        }),
                    },
                )
                await this.updateCart({}, response)

                Notify(window.config.custom_reorder.add_selected, 'success', [], window.url('/cart'))
                this.added = true
                setTimeout(() => this.added = false, 3000)
            } catch(error) {
                Notify(error.message, 'error')
            }

            this.adding = false
        },

        transformItem(item) {
            if (!item) {
                return null
            }

            // Only transform when the item is in OrderItem format
            if (this.cartItems) {
                return item
            }

            // Skip if already transformed
            if ('sku' in item) {
                return item
            }

            return {
                selected_options: item.selected_options ?? undefined,
                entered_options: item.entered_options ?? undefined,
                quantity: item.quantity_ordered,
                sku: item.product_sku,
            }
        },

        itemUrl(item) {
            return this.urls?.[item.product_sku] ?? null
        },

        itemId(item, id) {
            return btoa(`${this.transformItem(item).sku}|${id}`)
        },
    },

    computed: {
        transformedItems() {
            return this.items.map(this.transformItem)
        },
    },
}
</script>
