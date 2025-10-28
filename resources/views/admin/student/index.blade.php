@extends('layouts.app')

@section('content')
    <style>
        nav svg {
            height: 20px;
        }

        nav.hidden {
            display: block;
        }

        th {
            font-size: 0.875em;
        }

        .modal-content {
            transform: scale(0.8);
            transition: transform 0.3s ease-in-out;
        }

        .modal.show .modal-content {
            transform: scale(1);
        }
    </style>

    <x-table title="Daftar Data Siswa" createRoute="admin.student.create" showRoute="admin.student.show" 
        editRoute="admin.student.edit" deleteRoute="admin.student.destroy" 
        :data="$students"
        
        :columns="[
            ['label' => 'Foto', 'field' => 'image', 'html' => true],
            ['label' => 'Nama Siswa', 'field' => 'name'],
            ['label' => 'NIS', 'field' => 'nis'],
            ['label' => 'Tingkat Kelas', 'field' => 'grade_level'],
            ['label' => 'Jurusan Saat Ini', 'field' => 'current_major'],
            ['label' => 'Status Aktif', 'field' => 'is_active'],
        ]"
    />

    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Detail Foto Siswa</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid rounded shadow-lg"
                        style="max-height: 80vh; transition: 0.3s;">
                </div>
            </div>
        </div>
    </div>

    <script>
        function showImage(namaSiswa, src) {
            document.getElementById('imageModalLabel').innerText = 'Foto Profil: ' + namaSiswa;
            document.getElementById('modalImage').src = src;
        }

        document.addEventListener("DOMContentLoaded", function() {
            var modal = document.getElementById("imageModal");
            
            if (typeof bootstrap !== 'undefined') {
                modal.addEventListener("keydown", function(event) {
                    if (event.key === "Escape") {
                        var modalInstance = bootstrap.Modal.getInstance(modal);
                        modalInstance.hide();
                    }
                });

                var closeButton = document.querySelector("#imageModal .btn-close");
                closeButton.addEventListener("click", function() {
                    var modalInstance = bootstrap.Modal.getInstance(modal);
                    modalInstance.hide();
                });
            } else {
                 $('#imageModal').on('hide.bs.modal', function (e) {
                    $('#modalImage').attr('src', '');
                 });
            }
        });
    </script>
@endsection