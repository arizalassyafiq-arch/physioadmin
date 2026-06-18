@props(['messages' => []])

@php
    $messages = collect($messages)->filter()->values();
@endphp

@if ($messages->isNotEmpty())
    <div class="mt-2 space-y-1">
        @foreach ($messages as $message)
            <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
        @endforeach
    </div>
@endif
