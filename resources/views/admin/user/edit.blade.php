@extends('layouts.app')

@section('title', 'User')

@section('content')

    <div class="card">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Ubah Data Pengguna</h5>
        </div>
        <div class="card-body">
            <x-user_form :id="$user->id" route="admin.user.update" :imageRequired="false" :isReadOnly="true" method="PUT"
                :withRole="true" :name="$user->name" :email="$user->email" :withBack="true" routeBack="admin.user.index"
                :image="$user->image_name" :role="$user->is_admin" :passwordRequired="false" :deletePhotoProfile="true" />
        </div>
    </div>

@endsection
