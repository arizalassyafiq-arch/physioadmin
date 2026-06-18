@if (session('success'))
    <div class="flash-box success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="flash-box error">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="flash-box error">
        <p><strong>Periksa kembali data yang diisi.</strong></p>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
