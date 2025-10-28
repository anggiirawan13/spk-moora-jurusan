@extends('layouts.app')

@section('title', 'User')

@section('content')

    <x-alert />

    <div class="card">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Tambah Data Pengguna</h5>
        </div>
        <div class="card-body">
            <x-user_form route="admin.user.store" :imageRequired="false" :isReadOnly="false" method="POST" :withRole="true"
                name="" email="" :withBack="true" routeBack="admin.user.index" image="" role=""
                :deletePhotoProfile="false" :passwordRequired="true" />
        </div>
    </div>

@endsection
