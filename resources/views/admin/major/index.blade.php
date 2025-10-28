@extends('layouts.app')

@section('title', 'Jurusan')

@section('content')

    <x-table 
        title="Daftar Jurusan" 
        createRoute="admin.major.create" 
        showRoute="admin.major.show"
        editRoute="admin.major.edit"
        deleteRoute="admin.major.destroy"
        :data="$majors" 
        :columns="[
            ['label' => 'Kode', 'field' => 'code'],
            ['label' => 'Nama', 'field' => 'name'],
            ['label' => 'Deskripsi', 'field' => 'description'],
        ]"
    />

@endsection
