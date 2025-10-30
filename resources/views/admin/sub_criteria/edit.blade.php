@extends('layouts.app')

@section('title', 'Ubah Sub Kriteria')

@section('content')

    <x-alert />

    <div class="col-lg-12 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-edit"></i> Ubah Skala Nilai (Sub Kriteria)</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.subcriteria.update', $subCriteria->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Hidden input untuk kriteria_id --}}
                    <input type="hidden" name="criteria_id" value="{{ $subCriteria->criteria_id }}">
                    {{-- Hidden name akan diisi oleh JavaScript dari data-name radio button yang dipilih --}}
                    <input type="hidden" name="name" id="name_hidden" value="{{ old('name', $subCriteria->name) }}">

                    <div class="form-group">
                        <label for="criteria_display">Kriteria Induk (Mata Pelajaran)</label>
                        <input type="text" id="criteria_display" class="form-control font-weight-bold"
                            value="Jurusan: {{ $subCriteria->criteria->major->name ?? 'N/A' }} | Mapel: {{ $subCriteria->criteria->subject->name ?? 'N/A' }} (Bobot: {{ $subCriteria->criteria->weight }} | Atribut: {{ ucwords($subCriteria->criteria->attribute_type) }})"
                            disabled>
                    </div>

                    <hr>
                    <h6 class="font-weight-bold text-info mt-4 mb-3">
                        <i class="fas fa-check-circle"></i> Pilih Nilai Skala Baru:
                    </h6>
                    <p class="text-secondary">Pilih satu nilai skala di bawah ini. Opsi yang Sudah Dipakai oleh Sub Kriteria lain pada Kriteria Induk ini tidak dapat dipilih.</p>
                    
                    @php
                        // Data sub kriteria hardcode yang tersedia
                        $fixedSubCriteria = [
                            5 => 'Sangat Baik', 4 => 'Baik', 3 => 'Cukup', 2 => 'Kurang', 1 => 'Sangat Kurang',
                        ];
                        
                        // 1. Ambil nilai SPK (value) dari Sub Kriteria lain pada Kriteria Induk yang sama
                        $existingValues = $subCriteria->criteria->subCriteria->pluck('value')->toArray();

                        // 2. Tentukan nilai yang sedang di-edit
                        $oldValue = $subCriteria->value;
                        
                        // 3. Hapus nilai lama dari daftar 'existingValues' agar nilai tersebut bisa di-edit (dipilih)
                        $key = array_search($oldValue, $existingValues);
                        if ($key !== false) {
                            unset($existingValues[$key]);
                        }
                        
                        // Daftar nilai SPK yang TERLARANG/DISABLED
                        $disabledValues = $existingValues; 

                        // Nilai yang akan digunakan, prioritaskan old() dari hasil error, lalu nilai lama dari DB
                        $selectedValue = old('value', $subCriteria->value);
                    @endphp

                    <div class="row" id="radio-container">
                        @foreach ($fixedSubCriteria as $val => $name)
                            @php
                                $id = 'radio_val_' . $val;
                                $isChecked = ($selectedValue == $val);
                                $isCurrent = ($val == $subCriteria->value);
                                
                                // Cek apakah nilai ini sudah ada di Sub Kriteria lain
                                $isDisabled = in_array($val, $disabledValues); 
                                
                                // Tentukan kelas styling
                                $statusClass = $isChecked ? 'border-primary shadow-lg' : '';
                                $opacityClass = $isDisabled ? 'opacity-50' : ''; 
                                $badgeClass = $isDisabled ? 'badge-secondary' : 'badge-primary'; 
                            @endphp
                            <div class="col-md-4 col-lg-3 mb-3">
                                <label for="{{ $id }}" class="w-100 mb-0">
                                    {{-- Tambahkan class opacity-50 untuk styling disabled --}}
                                    <div class="card p-3 h-100 cursor-pointer radio-card {{ $statusClass }} {{ $opacityClass }}" style="cursor: {{ $isDisabled ? 'not-allowed' : 'pointer' }};">
                                        
                                        {{-- Radio Button --}}
                                        <input type="radio" name="value" id="{{ $id }}" 
                                            value="{{ $val }}" 
                                            data-name="{{ $name }}"
                                            {{ $isChecked ? 'checked' : '' }}
                                            {{ $isDisabled ? 'disabled' : '' }} 
                                            class="d-none required-radio">

                                        <p class="font-weight-bold mb-1">
                                            {{ $name }}
                                            @if ($isCurrent)
                                                <span class="badge badge-info ml-1">Nilai Saat Ini</span> 
                                            @endif
                                            @if ($isDisabled)
                                                <span class="badge badge-warning ml-1">Sudah Dipakai</span> 
                                            @endif
                                        </p>
                                        <p class="mb-0">Nilai SPK: <span class="badge {{ $badgeClass }} p-2">{{ $val }}</span></p>

                                    </div>
                                </label>
                            </div>
                        @endforeach
                        
                        @error('value')
                            <div class="col-12 mt-3">
                                <div class="alert alert-danger">
                                    <strong>Peringatan:</strong> {{ $message }}
                                </div>
                            </div>
                        @enderror
                    </div>
                    
                    <hr class="mt-4">

                    <a href="{{ route('admin.subcriteria.index', $subCriteria->criteria_id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<style>
    /* Style untuk membuat card tampak seperti tombol yang aktif saat dipilih */
    .radio-card {
        border: 2px solid #e3e6f0;
        transition: all 0.2s;
    }
    .radio-card:hover {
        border-color: #4e73df; /* Warna primary */
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }
    .radio-card.border-primary {
        border-color: #4e73df !important; /* Warna primary untuk yang terpilih */
    }
    /* Style untuk opsi yang disabled/sudah terpakai */
    .radio-card.opacity-50 {
        opacity: 0.5;
        background-color: #f8f9fa !important; /* Warna agak abu-abu */
        pointer-events: none; /* Menonaktifkan klik pada card */
        border: 2px solid #6c757d; /* Border sekunder/abu-abu */
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radioContainer = document.getElementById('radio-container');
        const nameHidden = document.getElementById('name_hidden');
        const radioCards = document.querySelectorAll('.radio-card');

        // Fungsi untuk mengupdate hidden name field dan styling card
        function updateSelection(selectedRadio) {
            // Hapus styling dari semua card
            radioCards.forEach(card => card.classList.remove('border-primary', 'shadow-lg'));
            
            // Tambahkan styling pada card yang dipilih
            if (selectedRadio) {
                // Temukan elemen .radio-card yang merupakan parent dari radio button
                const selectedCard = selectedRadio.closest('.radio-card');
                selectedCard.classList.add('border-primary', 'shadow-lg');
                
                // Update nilai hidden input 'name'
                nameHidden.value = selectedRadio.getAttribute('data-name');
            }
        }

        // Event listener untuk kontainer radio button
        radioContainer.addEventListener('change', function(e) {
            if (e.target.type === 'radio' && e.target.name === 'value' && !e.target.disabled) {
                updateSelection(e.target);
            }
        });

        // Inisialisasi pada saat DOMContentLoaded: pastikan styling dan hidden name terisi
        const initialSelectedRadio = document.querySelector('input[name="value"]:checked');
        updateSelection(initialSelectedRadio);
        
        // Pastikan hidden name terisi saat dimuat (redundansi untuk keamanan)
        if (initialSelectedRadio) {
             nameHidden.value = initialSelectedRadio.getAttribute('data-name');
        }
    });
</script>
@endpush