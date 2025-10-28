@extends('layouts.app')

@section('content')
    <x-table title="Daftar Akun" createRoute="admin.user.create" showRoute="admin.user.show" editRoute="admin.user.edit"
        deleteRoute="admin.user.destroy" :data="$users" :columns="[
            ['label' => 'Nama', 'field' => 'name'],
            ['label' => 'Email', 'field' => 'email'],
            ['label' => 'Peran', 'field' => 'is_admin', 'html' => true],
            ['label' => 'Tanggal Bergabung', 'field' => 'created_at', 'html' => true],
        ]" />
@endsection
