@extends('layouts.app')

@section('title', 'Mata Pelajaran')

@section('content')

    <x-table 
        title="Daftar Mata Pelajaran" 
        createRoute="admin.subject.create" 
        showRoute="admin.subject.show"
        editRoute="admin.subject.edit"
        deleteRoute="admin.subject.destroy"
        :data="$subjects" 
        :columns="[
            ['label' => 'Kode', 'field' => 'code'],
            ['label' => 'Nama', 'field' => 'name'],
        ]"
    />

@endsection
