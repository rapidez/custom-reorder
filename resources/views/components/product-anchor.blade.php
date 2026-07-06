<component
    v-bind:is="reorderSlotScope.itemUrl(item) && !reorderSlotScope.loading ? 'a' : 'div'"
    v-bind:href="reorderSlotScope.itemUrl(item)"
    {{ $attributes }}
>
    {{ $slot }}
</component>
