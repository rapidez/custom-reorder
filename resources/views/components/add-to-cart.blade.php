@props(['component' => 'rapidez::button.conversion'])

<div {{ $attributes->class('border-t flex p-2 sticky bottom-0 bg-white items-center') }}>
    <div>
        <template v-if="reorderSlotScope.selectedItems.length == 1">
            @lang(':count product selected', ['count' => '@{{ reorderSlotScope.selectedItems.length }}'])
        </template>
        <template v-else>
            @lang(':count products selected', ['count' => '@{{ reorderSlotScope.selectedItems.length }}'])
        </template>
    </div>
    <x-dynamic-component
        :$component
        class="ml-auto"
        v-bind:disabled="!reorderSlotScope.selectedItems.length || reorderSlotScope.adding"
        v-bind:class="{'button-loading': reorderSlotScope.adding}"
        v-on:click="reorderSlotScope.addToCart"
    >
        {{ $slot }}
    </x-dynamic-component>
</div>
