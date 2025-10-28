@extends('layouts.app')

@section('title', 'Kriteria')

@section('content')

    <x-table title="Daftar Kriteria" createRoute="admin.criteria.create" showRoute="admin.criteria.show" editRoute="admin.criteria.edit"
        deleteRoute="admin.criteria.destroy" :data="$criterias" :columns="[
            ['label' => 'Kode', 'field' => 'code'],
            ['label' => 'Nama', 'field' => 'name'],
            ['label' => 'Bobot', 'field' => 'weight'],
            ['label' => 'Atribut', 'field' => 'attribute_type'],
        ]" />

@endsection
