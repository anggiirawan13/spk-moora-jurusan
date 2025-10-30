@extends('layouts.app')

@section('title', 'Input/Edit Nilai Rapor: ' . $student->name)

@section('content')

    {{-- Asumsi Anda memiliki X-alert component --}}
    <x-alert />

    <div class="col-lg-12 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit"></i> Input/Edit Nilai Rapor Siswa
                </h5>
                <p class="text-secondary mt-1 mb-0">
                    Siswa: {{ $student->name }} | Jurusan: {{ $student->major->name ?? 'Belum Dijuruskan' }}
                </p>
                <hr class="mb-0 mt-3">
                <p class="text-info small mb-0 mt-2">
                    <i class="fas fa-info-circle"></i> Hanya Mata Pelajaran yang telah dijadikan Kriteria Penjurusan
                    yang akan muncul. Kosongkan nilai untuk menghapus data tersebut.
                </p>
            </div>
            <div class="card-body">

                {{-- Form diarahkan ke RaporController@store. Gunakan route 'rapor.store' dengan parameter $student --}}
                <form action="{{ route('admin.rapor.update', $student->id) }}" method="POST">
                    @csrf

                    <hr>

                    {{-- Loop untuk setiap Kriteria Mata Pelajaran (sudah difilter) --}}
                    @forelse ($criteria as $item)
                        @php
                            // Nilai rapor mentah yang sudah tersimpan
                            $oldRaporValue = $raporValues[$item->subject_id] ?? null;

                            // Nilai SPK hasil konversi yang sudah tersimpan
                            $oldSubCriteriaId = $convertedValues[$item->id] ?? null;

                            // Ambil detail SPK
                            $spkValue = $spkDetailsMap[$oldSubCriteriaId] ?? 'N/A';
                            $spkName = $spkNamesMap[$oldSubCriteriaId] ?? 'Belum Dikonversi';

                            // Tentukan kelas badge untuk status awal
                            $statusClass = ($spkValue != 'N/A') ? 'badge-success' : 'badge-secondary';
                            $textClass = ($spkValue != 'N/A') ? 'text-muted' : 'text-danger';

                            // Nama input array: values[criteria_id]
                            $inputValueName = "values[{$item->id}]";
                        @endphp

                        <div class="form-group row">
                            {{-- Label Mata Pelajaran (Kriteria) --}}
                            <label for="criteria_{{ $item->id }}" class="col-md-5 col-form-label font-weight-bold">
                                {{ $item->subject->name ?? 'Mata Pelajaran Tidak Ditemukan' }}
                                <small class="text-muted d-block font-weight-normal">({{ $item->subject->code ?? '' }}) | Bobot:
                                    {{ $item->weight }}</small>
                            </label>

                            <div class="col-md-3">
                                {{-- INPUT NILAI RAPOR MENTAH --}}
                                <input type="number"
                                    class="form-control value-input @error($inputValueName) is-invalid @enderror"
                                    name="{{ $inputValueName }}" id="criteria_{{ $item->id }}"
                                    data-criteria-id="{{ $item->id }}" {{-- DIGUNAKAN UNTUK AJAX --}}
                                    placeholder="Nilai (0-100)" value="{{ old($inputValueName, $oldRaporValue) }}" min="0"
                                    step="0.1" max="100">

                                @error($inputValueName)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                {{-- HIDDEN INPUT untuk mengirim data subject_id --}}
                                <input type="hidden" name="criteria_data[{{ $item->id }}][subject_id]"
                                    value="{{ $item->subject_id }}">
                            </div>

                            {{-- TEMPAT TAMPIL HASIL KONVERSI SPK --}}
                            <div class="col-md-4 d-flex align-items-center">
                                <span class="d-block font-weight-bold mr-2">Nilai SPK:</span>
                                <span class="spk-value-badge" id="spk-{{ $item->id }}">
                                    @if ($oldRaporValue && $spkValue != 'N/A')
                                        <span class="badge {{ $statusClass }} p-2 mr-2">{{ $spkValue }}</span>
                                        <span class="small {{ $textClass }}">({{ $spkName }})</span>
                                    @else
                                        <span class="badge badge-secondary p-2">N/A</span>
                                        <span class="small text-danger ml-2 font-weight-bold">
                                            {{ $oldRaporValue ? 'Belum Terkonversi' : 'Kosong' }}
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle"></i> Tidak ada Mata Pelajaran yang ditetapkan sebagai
                            kriteria untuk Jurusan {{ $student->major->name ?? 'Belum Dijuruskan' }}. Harap atur Kriteria
                            Penjurusan terlebih dahulu.
                        </div>
                    @endforelse

                    <div class="form-group mt-5">
                        <a href="{{ route('admin.rapor.index') }}" class="btn btn-secondary"><i
                                class="fas fa-arrow-left"></i>
                            Kembali</a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan & Konversi Nilai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @push('scripts')
        <script>
            $(document).ready(function () {
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                let typingTimer; // Variabel untuk menyimpan timer debouncing
                const doneTypingInterval = 500; // Jeda waktu (ms) setelah berhenti mengetik

                // Menangani perubahan pada semua input dengan class 'value-input'
                $('.value-input').on('keyup change', function () {
                    const inputElement = $(this);
                    const criteriaId = inputElement.data('criteria-id');
                    const inputValue = inputElement.val();
                    const targetElement = $('#spk-' + criteriaId);

                    // Hapus timer sebelumnya jika ada
                    clearTimeout(typingTimer);

                    // Atur timer baru
                    typingTimer = setTimeout(function () {
                        // Logika konversi hanya berjalan setelah jeda (debouncing)

                        // 1. Cek apakah input kosong, kurang dari 0, atau lebih dari 100
                        if (inputValue === '' || parseFloat(inputValue) < 0 || parseFloat(inputValue) > 100) {
                            targetElement.html('<span class="badge badge-secondary p-2">N/A</span><span class="small text-danger ml-2 font-weight-bold">Kosong</span>');

                            // Jika input kosong, kirim nilai null/kosong ke controller untuk konfirmasi 'Kosong'
                            if (inputValue === '') {
                                sendAjax(criteriaId, null, targetElement, csrfToken);
                            }
                            return;
                        }

                        // 2. Jika nilai valid, kirim AJAX
                        sendAjax(criteriaId, inputValue, targetElement, csrfToken);

                    }, doneTypingInterval); // Jeda 500 ms sebelum eksekusi

                });

                /
                 * Fungsi untuk mengirim permintaan AJAX ke endpoint konversi nilai.
                 */
                function sendAjax(criteriaId, value, targetElement, token) {
                    // Tampilkan loading state sementara menunggu respons
                    targetElement.html('<i class="fas fa-spinner fa-spin text-primary"></i>');

                    $.ajax({
                        url: "{{ route('admin.rapor.convert-value') }}",
                        method: 'POST',
                        data: {
                            _token: token,
                            criteria_id: criteriaId,
                            value: value // Mengirim null jika input kosong
                        },
                        dataType: 'json',
                        success: function (response) {
                            // Perbarui tampilan berdasarkan respons dari server
                            let badgeClass = response.status_class || 'badge-secondary';
                            let textClass = (response.spk_value !== 'N/A') ? 'text-muted' : 'text-danger font-weight-bold';

                            targetElement.html(
                                `<span class="badge ${badgeClass} p-2 mr-2">${response.spk_value}</span>
                                        <span class="small ${textClass}">(${response.spk_name})</span>`
                            );
                        },
                        error: function (xhr) {
                            console.error("AJAX Error: Konversi gagal.", xhr.responseText);
                            targetElement.html('<span class="badge badge-danger p-2">Error</span>');
                        }
                    });
                }
            });
        </script>
    @endpush
@endpush