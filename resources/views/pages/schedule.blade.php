@php
    $todayName = now()->locale('id')->translatedFormat('l');
    $todaySchedule = collect($schedule)->firstWhere('day', $todayName);
@endphp

<x-layouts.app :title="'Jadwal Terapis | PhysioAdmin'" :header="'Jadwal Terapis'">
    <div class="space-y-6">
        <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-blue-600">Jam Praktik</p>
                    <h2 class="mt-3 text-2xl font-bold text-slate-900">Jadwal Praktik Terapis</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-600">
                        Jadwal praktik reguler fisioterapi untuk Senin sampai Sabtu.
                    </p>
                </div>

                <div class="rounded-2xl border border-blue-100 bg-blue-50 px-5 py-4 text-sm">
                    <p class="font-semibold text-blue-950">Status hari ini</p>
                    <p class="mt-1 text-blue-800">
                        @if ($todaySchedule && $todaySchedule['is_open'])
                            Buka pukul {{ $todaySchedule['hours'] }}
                        @else
                            Tidak ada jadwal praktik
                        @endif
                    </p>
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
            <div class="grid border-b border-slate-200 bg-slate-50 px-5 py-3 text-xs font-bold uppercase tracking-[0.18em] text-slate-500 sm:grid-cols-[1fr_1fr_140px]">
                <div>Hari</div>
                <div class="hidden sm:block">Jam Praktik</div>
                <div class="hidden sm:block">Status</div>
            </div>

            <div class="divide-y divide-slate-200">
                @foreach ($schedule as $item)
                    @php
                        $isToday = $item['day'] === $todayName;
                    @endphp

                    <div class="grid gap-3 px-5 py-4 sm:grid-cols-[1fr_1fr_140px] sm:items-center {{ $isToday ? 'bg-blue-50/70' : 'bg-white' }}">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $item['day'] }}</p>
                            @if ($isToday)
                                <p class="mt-1 text-xs font-semibold text-blue-700">Hari ini</p>
                            @endif
                        </div>

                        <div>
                            <p class="text-sm font-semibold {{ $item['is_open'] ? 'text-slate-800' : 'text-slate-400' }}">
                                {{ $item['hours'] }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500 sm:hidden">
                                {{ $item['is_open'] ? 'Praktik tersedia' : 'Tidak ada praktik' }}
                            </p>
                        </div>

                        <div>
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $item['is_open'] ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-slate-100 text-slate-500 ring-1 ring-slate-200' }}">
                                {{ $item['is_open'] ? 'Buka' : 'Libur' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-layouts.app>
