@props(['metaTitle' => null, 'metaDescription' => null, 'ogType' => 'website', 'ogImage' => null])

@include('layouts.storefront', [
    'slot' => $slot,
    'metaTitle' => $metaTitle,
    'metaDescription' => $metaDescription,
    'ogType' => $ogType,
    'ogImage' => $ogImage,
])
