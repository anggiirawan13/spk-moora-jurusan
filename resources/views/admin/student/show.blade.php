@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="m-0 font-weight-bold text-primary">Detail Siswa: {{ $student->name }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <img src="{{ $student->profile_picture ? asset('storage/student/' . $student->profile_picture) : asset('img/default-profile.png') }}"
                            class="img-fluid rounded shadow" alt="{{ $student->name }}">
                    </div>
                    <div class="col-md-8">
                        <x-table_student :student="$student" /> 
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <x-button_back route="admin.student.index" /> 
                @if (auth()->user()->is_admin == 1)
                    <x-button_edit route="admin.student.edit" :id="$student->id" /> 
                @endif
            </div>
        </div>
    </div>

@endsection
