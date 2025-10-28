@extends('layouts.app')

@section('title', 'User')

@section('content')

    <div class="card">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Ubah Profil</h5>
        </div>
        <div class="card-body">
            <x-user_form route="profile.update" :imageRequired="false" :isReadOnly="true" method="PUT" :withRole="false"
                :name="auth()->user()->name" :email="auth()->user()->email" :withBack="false" routeBack="" :image="auth()->user()->image_name" role=""
                :deletePhotoProfile="true" :passwordRequired="false" />
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var imgElement = document.getElementById('imagePreview');

            @if (auth()->user()->image_name)
                imgElement.src = "{{ asset('storage/user/' . auth()->user()->image_name) }}";
                imgElement.style.display = 'block';
            @endif
        });
    </script>

@endsection
