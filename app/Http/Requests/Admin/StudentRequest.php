<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $studentId = $this->route('student'); 

        return [
            'nis' => [
                'required', 
                'string', 
                Rule::unique('students', 'nis')->ignore($studentId),
            ],
            'name' => 'required|string|max:255',
            'email' => [
                'nullable', 
                'email', 
                Rule::unique('students', 'email')->ignore($studentId),
            ],
            'grade_level' => 'required|integer|min:10|max:12',
            'major_id' => 'nullable|exists:majors,id',
            'user_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
            'is_active' => 'required|integer|in:0,1',
        ];
    }
}
