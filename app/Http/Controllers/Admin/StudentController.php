<?php

namespace App\Http\Controllers\Admin;

use App\Models\Student;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StudentRequest;
use App\Models\Major;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = Student::with('major')->latest()->get(); 

        $students->transform(function ($student) {
            $majorName = $student->major?->name ?? 'Belum Dijuruskan';
            
            $imagePath = 'storage/student/' . ($student->profile_picture ?? 'default-profile.png');
            $imageAsset = asset($imagePath);

            return [
                'id' => $student->id,
                'image' => '<a href="#" data-toggle="modal" data-target="#imageModal" onclick="showImage(\'' . $student->name . '\', \'' . $imageAsset . '\')">
                                 <img class="default-img" src="' . $imageAsset . '" width="60">
                            </a>',
                'nis' => $student->nis,
                'name' => $student->name,
                'grade_level' => $student->grade_level,
                'current_major' => $majorName,
                'is_active' => $student->is_active ? 'Aktif' : 'Tidak Aktif', 
            ];
        });

        return view('admin.student.index', compact('students')); 
    }

    public function create(): View
    {
        $majors = Major::all();

        return view('admin.student.create', compact('majors'));
    }

    public function store(StudentRequest $request): RedirectResponse
    {
        if ($request->validated()) {
            $data = $request->except(['profile_picture']);

            if ($request->hasFile('profile_picture')) {
                $image = $request->file('profile_picture')->store('student', 'public'); 
                $data['profile_picture'] = basename($image); // Simpan nama file
            }

            Student::create($data); 
        }

        return redirect()->route('admin.student.index')->with('success', 'Data Siswa berhasil disimpan');
    }

    public function show($id): View
    {
        $student = Student::with(['major'])->findOrFail($id); 
        
        return view('admin.student.show', compact('student'));
    }

    public function edit($id): View
    {
        $student = Student::findOrFail($id);
        $majors = Major::all();

        return view('admin.student.edit', compact('student', 'majors'));
    }

    public function update(StudentRequest $request, Student $student): RedirectResponse
    {
        if ($request->validated()) {
            $dataUpdate = $request->except('profile_picture'); 
            
            if ($request->hasFile('profile_picture')) { 
                if ($student->profile_picture) { 
                    Storage::delete('public/student/' . $student->profile_picture); 
                }

                $image = $request->file('profile_picture')->store('student', 'public');
                $imageName = basename($image);

                $dataUpdate['profile_picture'] = $imageName;
            }

            $student->update($dataUpdate); 
        }

        return redirect()->route('admin.student.index')->with('success', 'Data Siswa berhasil diubah');
    }

    public function destroy(Student $student): RedirectResponse
    {
        if ($student->profile_picture) {
             Storage::delete('public/student/' . $student->profile_picture);
        }

        $student->delete(); 

        return redirect()->route('admin.student.index')->with('success', 'Data Siswa berhasil dihapus');
    }
}
