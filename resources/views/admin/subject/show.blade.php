@extends('layouts.app')

@section('title', 'Mata Pelajaran')

@section('content')

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="m-0 font-weight-bold text-primary">Detail Mata Pelajaran: {{ $subject->name }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Kode Mata Pelajaran</th>
                        <td>{{ $subject->code }}</p>
                        </td>
                    </tr>

                    <tr>
                        <th>Nama Mata Pelajaran</th>
                        <td>{{ $subject->name }}</p>
                        </td>
                    </tr>

                    <tr>
                        <th>Dibuat Pada</th>
                        <td>{{ $subject->created_at->format('d-m-Y H:i') }}</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th>Diperbarui Pada</th>
                        <td>{{ $subject->updated_at->format('d-m-Y H:i') }}</p>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.subject.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>
                    Kembali</a>
                @if (auth()->user()->is_admin == 1)
                <x-button_edit route="admin.subject.edit" :id="$subject->id" />
                @endif
            </div>
        </div>
    </div>

@endsection
