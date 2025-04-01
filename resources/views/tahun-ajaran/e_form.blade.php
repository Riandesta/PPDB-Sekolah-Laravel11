<x-layout>
    <x-slot name="title">Edit Tahun Ajaran</x-slot>
    <x-slot name="card_title">Edit Tahun Ajaran</x-slot>

    <h1>{{ isset($tahunAjaran) ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran' }}</h1>

    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-body">
<<<<<<< Updated upstream:resources/views/tahun-ajaran/e_form.blade.php
                <form action="{{ route('tahun-ajaran.update', $tahunAjaran->id) }}" method="POST" id="tahunAjaranForm">
                    @csrf
                    @method('PUT')
=======
                <form
                action="{{ request()->routeIs('tahun-ajaran.create') ? route('tahun-ajaran.store') : route('tahun-ajaran.update', ['tahunAjaran' => $tahunAjaran->id]) }}"
                method="POST"
                enctype="multipart/form-data">
                @csrf
                @if (request()->routeIs('tahun-ajaran.update'))
                    @method('PUT')
                @endif
>>>>>>> Stashed changes:resources/views/tahun-ajaran/form.blade.php

                    <div class="row g-4">
                        {{-- Left column - Basic Information --}}
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">Informasi Dasar</h5>
                                </div>
                                <div class="card-body">
                                    {{-- Tahun Ajaran Input --}}
                                    <div class="mb-3">
                                        <label class="form-label required">Tahun Ajaran</label>
                                        <input type="text" name="tahun_ajaran"
                                            class="form-control @error('tahun_ajaran') is-invalid @enderror"
                                            value="{{ old('tahun_ajaran', $tahunAjaran->tahun_ajaran) }}" placeholder="Contoh: 2024/2025" required>
                                        @error('tahun_ajaran')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            {{-- Tahun Mulai Input --}}
                                            <div class="mb-3">
                                                <label class="form-label required">Tahun Mulai</label>
                                                <input type="number" name="tahun_mulai"
                                                    class="form-control @error('tahun_mulai') is-invalid @enderror"
                                                    value="{{ old('tahun_mulai', $tahunAjaran->tahun_mulai) }}" min="2000" max="2099" required>
                                                @error('tahun_mulai')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            {{-- Tahun Selesai Input --}}
                                            <div class="mb-3">
                                                <label class="form-label required">Tahun Selesai</label>
                                                <input type="number" name="tahun_selesai"
                                                    class="form-control @error('tahun_selesai') is-invalid @enderror"
                                                    value="{{ old('tahun_selesai', $tahunAjaran->tahun_selesai) }}" min="2000" max="2099" required>
                                                @error('tahun_selesai')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            {{-- Tanggal Mulai Input --}}
                                            <div class="mb-3">
                                                <label class="form-label required">Tanggal Mulai</label>
                                                <input type="date" name="tanggal_mulai"
                                                    class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                                    value="{{ old('tanggal_mulai', $tahunAjaran->tanggal_mulai ? $tahunAjaran->tanggal_mulai->format('Y-m-d') : '') }}" required>
                                                @error('tanggal_mulai')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            {{-- Tanggal Selesai Input --}}
                                            <div class="mb-3">
                                                <label class="form-label required">Tanggal Selesai</label>
<<<<<<< Updated upstream:resources/views/tahun-ajaran/e_form.blade.php
                                                <input type="date"
                                                       name="tanggal_selesai"
                                                       class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                                       value="{{ old('tanggal_selesai', $tahunAjaran->tanggal_selesai ? $tahunAjaran->tanggal_selesai->format('Y-m-d') : '') }}" required>
=======
                                                <input type="date" name="tanggal_selesai"
                                                    class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                                    value="{{ old('tanggal_selesai', $tahunAjaran->tanggal_selesai ? $tahunAjaran->tanggal_selesai->format('Y-m-d') : '') }}"
                                                    required>
>>>>>>> Stashed changes:resources/views/tahun-ajaran/form.blade.php
                                                @error('tanggal_selesai')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Right column - Financial Information --}}
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">Informasi Biaya</h5>
                                </div>
                                <div class="card-body">
                                    {{-- Biaya Inputs --}}
                                    @foreach (['pendaftaran', 'ppdb', 'mpls', 'awal_tahun'] as $biaya)
                                        <div class="mb-3">
                                            <label class="form-label required">Biaya
                                                {{ ucfirst(str_replace('_', ' ', $biaya)) }}</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" name="biaya_{{ $biaya }}"
                                                    class="form-control @error('biaya_' . $biaya) is-invalid @enderror"
                                                    value="{{ old('biaya_' . $biaya, $tahunAjaran->{'biaya_' . $biaya}) }}" min="0" required>
                                            </div>
                                            @error('biaya_' . $biaya)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endforeach

                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="is_active"
                                                class="form-check-input @error('is_active') is-invalid @enderror"
                                                value="1"
                                                {{ old('is_active', $tahunAjaran->is_active ?? false) ? 'checked' : '' }}
                                                id="isActiveSwitch">
                                            <label class="form-check-label" for="isActiveSwitch">
                                                Aktifkan Tahun Ajaran Ini
                                            </label>
                                        </div>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Buttons --}}
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('tahun-ajaran.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Auto-calculate tahun_selesai
                $('input[name="tahun_mulai"]').on('change', function() {
                    let tahunMulai = parseInt($(this).val());
                    $('input[name="tahun_selesai"]').val(tahunMulai + 1);

                    // Auto-fill tahun_ajaran
                    $('input[name="tahun_ajaran"]').val(tahunMulai + '/' + (tahunMulai + 1));
                });

                // Validate tanggal_selesai is after tanggal_mulai
                $('input[name="tanggal_mulai"]').on('change', function() {
                    let tanggalMulai = $(this).val();
                    $('input[name="tanggal_selesai"]')
                        .attr('min', tanggalMulai)
                        .val(tanggalMulai);
                });

                // Form validation
                $('#tahunAjaranForm').on('submit', function(e) {
                    let tahunMulai = parseInt($('input[name="tahun_mulai"]').val());
                    let tahunSelesai = parseInt($('input[name="tahun_selesai"]').val());

                    if (tahunSelesai <= tahunMulai) {
                        e.preventDefault();
                        alert('Tahun Selesai harus lebih besar dari Tahun Mulai');
                        return false;
                    }
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .required:after {
                content: ' *';
                color: red;
            }

            .card {
                transition: all 0.3s ease;
            }

            .card:hover {
                transform: translateY(-2px);
            }

            .input-group-text {
                min-width: 45px;
                justify-content: center;
            }
        </style>
    @endpush
</x-layout>
