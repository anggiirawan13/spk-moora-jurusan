@props(['student'])

<table class="table table-bordered">
    
    <tr>
        <th>NIS</th>
        <td>{{ $student->nis }}</td>
    </tr>
    
    <tr>
        <th>Nama Siswa</th>
        <td>{{ $student->name }}</td>
    </tr>
    
    <tr>
        <th>Email</th>
        <td>{{ $student->email ?? '-' }}</td>
    </tr>

    <tr>
        <th>Tingkat Kelas</th>
        <td>Kelas {{ $student->grade_level }}</td>
    </tr>

    <tr>
        <th>Jurusan Saat Ini</th>
        <td>{{ $student->major?->name ?? 'Belum Dijuruskan' }}</td> 
    </tr>
    
    <tr>
        <th>Status Keaktifan</th>
        <td>{{ $student->is_active == 1 ? 'Aktif' : 'Tidak Aktif' }}</td>
    </tr>
    
    <tr>
        <th>Keterangan Tambahan</th>
        <td>{{ $student->description ?? '-' }}</td>
    </tr>

</table>
