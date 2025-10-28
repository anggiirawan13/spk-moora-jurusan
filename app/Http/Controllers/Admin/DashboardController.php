<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alternative;
use App\Models\Criteria;
use App\Models\Student;
use App\Models\Major;
use App\Models\Subject;
use App\Models\FuelType;
use App\Models\TransmissionType;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $student = Student::count();
        $users = User::count();
        $majors = Major::count();
        $subjects = Subject::count();
        $criteria = Criteria::count();
        $alternative = Alternative::count();

        $data = (object) [
            'subjects' => $subjects,
            'majors' => $majors,
            'students' => $student,
            'users' => $users,
            'criteria' => $criteria,
            'alternative' => $alternative,
        ];

        return view('admin.dashboard', compact('data'));
    }
}
