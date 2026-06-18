<style>
    .patient-category-option {
        border-color: #cbd5e1;
        background: #ffffff;
    }

    .patient-category-option:hover {
        border-color: #bfdbfe;
    }

    .patient-category-option:has(input:checked) {
        border-color: #3b82f6;
        background: #eff6ff;
        box-shadow: 0 0 0 2px #dbeafe;
    }
</style>

<div x-data="patientForm(
    @js(old('tanggal_lahir', isset($patient?->tanggal_lahir) ? $patient->tanggal_lahir->format('Y-m-d') : '')),
    @js($patient->umur ?? '')
)">
    <div class="grid gap-5 md:grid-cols-2">
        <div class="md:col-span-2">
            <fieldset>
                <legend class="mb-3 block text-sm font-medium text-slate-700">Kategori Pasien</legend>
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach (['dewasa' => 'Pasien Dewasa', 'anak' => 'Pasien Anak-anak'] as $value => $label)
                        @php
                            $isSelected = old('kategori_pasien', $patient->kategori_pasien ?? 'dewasa') === $value;
                        @endphp

                        <label class="patient-category-option flex cursor-pointer items-center gap-3 rounded-2xl border px-4 py-4 transition">
                            <input
                                type="radio"
                                name="kategori_pasien"
                                value="{{ $value }}"
                                class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500"
                                @checked($isSelected)
                                required
                            >
                            <span>
                                <span class="block text-sm font-semibold text-slate-900">{{ $label }}</span>
                                <span class="mt-1 block text-xs text-slate-500">
                                    {{ $value === 'dewasa' ? 'Untuk pasien usia dewasa.' : 'Untuk pasien anak-anak.' }}
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>
                <x-field-error :messages="$errors->get('kategori_pasien')" />
            </fieldset>
        </div>

        <div class="md:col-span-2">
            <label for="nama" class="mb-2 block text-sm font-medium text-slate-700">Nama Lengkap</label>
            <input id="nama" name="nama" type="text" value="{{ old('nama', $patient->nama ?? '') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>
            <x-field-error :messages="$errors->get('nama')" />
        </div>
        <div>
            <label for="no_rm" class="mb-2 block text-sm font-medium text-slate-700">No. RM</label>
            <input id="no_rm" name="no_rm" type="text" value="{{ old('no_rm', $patient->no_rm ?? '') }}" placeholder="00-00-00" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>
            <x-field-error :messages="$errors->get('no_rm')" />
        </div>
        <div>
            <label for="jenis_kelamin" class="mb-2 block text-sm font-medium text-slate-700">Jenis Kelamin</label>
            <select id="jenis_kelamin" name="jenis_kelamin" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>
                <option value="">Pilih jenis kelamin</option>
                <option value="L" @selected(old('jenis_kelamin', $patient->jenis_kelamin ?? '') === 'L')>Laki-laki</option>
                <option value="P" @selected(old('jenis_kelamin', $patient->jenis_kelamin ?? '') === 'P')>Perempuan</option>
            </select>
            <x-field-error :messages="$errors->get('jenis_kelamin')" />
        </div>
        <div>
            <label for="tanggal_lahir" class="mb-2 block text-sm font-medium text-slate-700">Tanggal Lahir</label>
            <input id="tanggal_lahir" name="tanggal_lahir" type="date" value="{{ old('tanggal_lahir', isset($patient?->tanggal_lahir) ? $patient->tanggal_lahir->format('Y-m-d') : '') }}" @input="syncAge($event.target.value)" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>
            <x-field-error :messages="$errors->get('tanggal_lahir')" />
        </div>
        <div>
            <p class="mb-2 block text-sm font-medium text-slate-700">Umur</p>
            <div class="flex min-h-11 items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800">
                <span x-text="ageLabel"></span>
            </div>
            <p class="mt-2 text-xs text-slate-500">Otomatis dihitung dari tanggal lahir.</p>
        </div>
        <div>
            <label for="pekerjaan" class="mb-2 block text-sm font-medium text-slate-700">Pekerjaan</label>
            <input id="pekerjaan" name="pekerjaan" type="text" value="{{ old('pekerjaan', $patient->pekerjaan ?? '') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
            <x-field-error :messages="$errors->get('pekerjaan')" />
        </div>
        <div class="md:col-span-2">
            <label for="alamat" class="mb-2 block text-sm font-medium text-slate-700">Alamat</label>
            <textarea id="alamat" name="alamat" rows="4" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old('alamat', $patient->alamat ?? '') }}</textarea>
            <x-field-error :messages="$errors->get('alamat')" />
        </div>
    </div>
</div>
