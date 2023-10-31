@props(['active'])

@php
    $classes = $active ?? false ?
    'inline-flex items-center items-center hover:text-yellow-900 text-sm text-yellow-500' :
    'inline-flex items-center items-center hover:text-yellow-900 text-sm text-grey-500';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
