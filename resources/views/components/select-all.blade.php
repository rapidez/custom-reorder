@props(['component' => 'rapidez::button.primary'])

<x-dynamic-component
    :$component
    v-bind:disabled="$root.loading.value"
    v-on:click="window.$emit('rapidez:reorder-all', true)"
>
    {{ $slot }}
</x-dynamic-component>
