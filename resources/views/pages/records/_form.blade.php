@if (($patient->kategori_pasien ?? 'dewasa') === 'anak')
    @include('pages.records._pediatric_form')
@else
    @include('pages.records._adult_form')
@endif
