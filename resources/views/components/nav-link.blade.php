@props(['active', 'navigate'])

@php
    $classes = $active ?? false ?
    'inline-flex items-center items-center hover:text-yellow-900 text-sm text-yellow-500' :
    'inline-flex items-center items-center hover:text-yellow-900 text-sm text-grey-500';
@endphp

<a {{ $navigate ?? true ? 'wire:navigate' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
