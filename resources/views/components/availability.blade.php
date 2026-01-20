<div v-if="reorderSlotScope.canReorder(item) && !reorderSlotScope.loading && !reorderSlotScope.matchingItems.includes(reorderSlotScope.itemId(item, index))">
    <template v-if="reorderSlotScope.unconfiguredItems?.includes(reorderSlotScope.itemId(item, index))">
        (@lang('Product needs to be configured'))
    </template>
    <template v-else>
        (@lang('Product not available'))
    </template>
</div>
