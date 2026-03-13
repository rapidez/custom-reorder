<div
    class="flex w-full"
    v-bind:class="{
        'opacity-50 cursor-not-allowed!': !reorderSlotScope.loading && !reorderSlotScope.matchingItems.includes(reorderSlotScope.itemId(item, index)),
    }"
>
    <template v-if="reorderSlotScope.canReorder(item)">
        <label
            class="flex items-center cursor-pointer pr-3"
            v-bind:class="{ 'cursor-not-allowed!': !reorderSlotScope.loading && !reorderSlotScope.matchingItems.includes(reorderSlotScope.itemId(item, index)) }"
        >
            <x-rapidez::input.checkbox
                class="border-emphasis"
                type="checkbox"
                v-bind:value="reorderSlotScope.itemId(item, index)"
                v-bind:disabled="reorderSlotScope.adding || !reorderSlotScope.matchingItems.includes(reorderSlotScope.itemId(item, index))"
                v-model="reorderSlotScope.selectedItems"
            />
        </label>
    </template>

    <div {{ $attributes->class('w-full') }}>
        {{ $slot }}
    </div>
</div>
