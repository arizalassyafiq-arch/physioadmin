@props([
    'id',
    'name',
    'value' => '',
    'required' => false,
    'placeholder' => 'mm/dd/yyyy',
])

<div {{ $attributes->merge(['class' => 'relative']) }} x-data="dateInputField(@js(\App\Support\DateInput::display($value)))">
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="text"
        inputmode="numeric"
        placeholder="{{ $placeholder }}"
        x-model="display"
        @input="syncNative"
        class="h-11 w-full rounded-xl border border-slate-300 px-4 pr-12 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
        @required($required)
    >
    <input
        type="date"
        x-ref="native"
        x-model="nativeValue"
        @change="fromNative"
        class="absolute border-0 p-0"
        style="right: 0; bottom: 0; width: 1px; height: 1px; opacity: 0; pointer-events: none;"
        tabindex="-1"
        aria-hidden="true"
    >
    <button
        type="button"
        @click="openPicker"
        class="absolute right-2 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-200"
        aria-label="Pilih tanggal"
    >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
        </svg>
    </button>
</div>
