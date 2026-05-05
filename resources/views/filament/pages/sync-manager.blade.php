<x-filament-panels::page>
    <x-filament::section>
        {{-- BAGIAN TOMBOL ATAS --}}
        <div style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 20px;">
            <h2 style="font-size: 1.2rem; font-weight: bold;">Kontrol Sinkronisasi</h2>
            
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                {{-- Tombol Hitung Ulang --}}
                <x-filament::button wire:click="refreshData" color="gray" variant="outline" icon="heroicon-m-arrow-path">
                    <span wire:loading.remove wire:target="refreshData">1. Hitung Ulang Data</span>
                    <span wire:loading wire:target="refreshData">Sabar, lagi ngitung...</span>
                </x-filament::button>

                {{-- Tombol Sinkron Semua --}}
                <x-filament::button wire:click="syncAll" color="warning" icon="heroicon-m-cloud-arrow-up">
                    <span wire:loading.remove wire:target="syncAll">2. SINKRONKAN SEMUA TABEL</span>
                    <span wire:loading wire:target="syncAll">Lagi proses sinkron masal...</span>
                </x-filament::button>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid rgba(128,128,128,0.2); margin-bottom: 20px;">

        {{-- INPUT CARI --}}
        <div style="margin-bottom: 20px; max-width: 400px;">
            <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
                <x-filament::input 
                    type="text" 
                    wire:model.live.debounce.500ms="search" 
                    placeholder="Cari nama tabel SIPP di sini..." 
                />
            </x-filament::input.wrapper>
        </div>

        {{-- TABEL DATA --}}
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="font-size: 12px; color: gray; text-transform: uppercase; border-bottom: 2px solid rgba(128,128,128,0.2);">
                        <th style="padding: 10px;">Nama Tabel</th>
                        <th style="padding: 10px; text-align: center;">Lokal</th>
                        <th style="padding: 10px; text-align: center;">Backup</th>
                        <th style="padding: 10px; text-align: center;">Selisih</th>
                        <th style="padding: 10px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px;">
                    @forelse($this->getStats() as $stat)
                        <tr style="border-bottom: 1px solid rgba(128,128,128,0.1);">
                            <td style="padding: 12px 10px; font-family: monospace; font-weight: bold; color: rgb(var(--primary-600));">
                                {{ $stat['name'] }}
                            </td>
                            <td style="padding: 12px 10px; text-align: center;">{{ number_format($stat['local']) }}</td>
                            <td style="padding: 12px 10px; text-align: center; opacity: 0.7;">{{ $stat['exists_in_target'] ? number_format($stat['backup']) : '-' }}</td>
                            <td style="padding: 12px 10px; text-align: center;">
                                @if($stat['gap'] == 0)
                                    <span>✅</span>
                                @else
                                    <x-filament::badge color="danger">
                                        +{{ number_format($stat['gap']) }}
                                    </x-filament::badge>
                                @endif
                            </td>
                            <td style="padding: 12px 10px; text-align: right;">
                                @if($stat['gap'] != 0 && $stat['exists_in_target'])
                                    <x-filament::link wire:click="syncTable('{{ $stat['name'] }}')" style="font-size: 11px; font-weight: bold;">
                                        SINKRON SATUAN
                                    </x-filament::link>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 40px; text-align: center; color: gray;">
                                Klik "Hitung Ulang" dulu atau tabel tidak ditemukan...
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>