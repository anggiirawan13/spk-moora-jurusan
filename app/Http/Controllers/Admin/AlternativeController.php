<?php

namespace App\Http\Controllers\Admin;

use App\Models\Criteria;
use App\Models\Alternative;
use App\Models\AlternativeValue;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SubCriteria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlternativeController extends Controller
{
    public function index(): View
    {
        $criterias = Criteria::with(['major', 'subject'])->orderBy('id')->get();

        $alternatives = Alternative::with([
            'values.subCriteria.criteria',
            'student.major'
        ])->get();

        $dataAlternatives = $alternatives->map(function ($alt) use ($criterias) {

            $student = $alt->student;

            $data = [
                'id'         => $alt->id,
                'name'       => $student->name ?? 'Siswa Tidak Ditemukan',
                'nis'       => $student->nis ?? '-',
                'major_name' => $student->major->name ?? '-',
            ];

            return $data;
        });

        return view('admin.alternative.index', [
            'criterias'    => $criterias,
            'alternatives' => $dataAlternatives,
        ]);
    }

    public function create(): View
    {
        $existingAlternativeStudentIds = Alternative::pluck('student_id');
        $students = Student::whereNotIn('id', $existingAlternativeStudentIds)->get();

        $criteriaCollection = Criteria::with(['major', 'subject', 'subCriteria'])
            ->orderBy('major_id', 'asc')
            ->orderBy('subject_id', 'asc')
            ->get();

        $groupedByMajor = $criteriaCollection->groupBy('major_id');

        $majorsWithCriteria = collect();
        foreach ($groupedByMajor as $criteriaGroup) {
            $major = $criteriaGroup->first()->major;

            $major->criteria = $criteriaGroup;

            $majorsWithCriteria->push($major);
        }

        return view('admin.alternative.create', [
            'students' => $students,
            'majorsWithCriteria' => $majorsWithCriteria,
        ]);
    }

    public function show(Alternative $alternative): View
    {
        $alternative->load([
            'student.major',
            'values.subCriteria.criteria.major',
            'values.subCriteria.criteria.subject',
        ]);

        $groupedValues = $alternative->values->groupBy(function ($value) {
            return $value->subCriteria->criteria->major_id;
        });

        $majorsWithValues = collect();
        foreach ($groupedValues as $majorId => $valuesGroup) {
            $major = $valuesGroup->first()->subCriteria->criteria->major;

            $major->criteria_groups = $valuesGroup->groupBy(function ($value) {
                return $value->subCriteria->criteria_id;
            });

            $majorsWithValues->push($major);
        }

        return view('admin.alternative.show', [
            'alternative' => $alternative,
            'majorsWithValues' => $majorsWithValues,
        ]);
    }

    public function edit(Alternative $alternative): View
    {
        $alternative->load('student');

        $selectedSubs = $alternative->values
            ->pluck('sub_criteria_id', 'subCriteria.criteria_id')
            ->toArray();

        $criteriaCollection = Criteria::with(['major', 'subject', 'subCriteria'])
            ->orderBy('major_id', 'asc')
            ->orderBy('subject_id', 'asc')
            ->get();

        $groupedByMajor = $criteriaCollection->groupBy('major_id');

        $majorsWithCriteria = collect();
        foreach ($groupedByMajor as $criteriaGroup) {
            $major = $criteriaGroup->first()->major;
            $major->criteria = $criteriaGroup;
            $majorsWithCriteria->push($major);
        }

        return view('admin.alternative.edit', [
            'alternative' => $alternative,
            'majorsWithCriteria' => $majorsWithCriteria,
            'selectedSubs' => $selectedSubs,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'student_id' => 'required|numeric|unique:alternatives,student_id|exists:students,id',
            'criteria' => 'required|array',
            'criteria.*' => 'required|numeric|exists:sub_criterias,id',
        ]);

        $alternative = Alternative::create([
            'student_id' => $request->student_id,
        ]);

        foreach ($request->criteria as $subCriteriaId) {
            $sub = SubCriteria::find($subCriteriaId);

            AlternativeValue::create([
                'alternative_id'    => $alternative->id,
                'sub_criteria_id'   => $subCriteriaId,
                'value'             => $sub->value ?? 0,
            ]);
        }

        return redirect()->route('admin.alternative.index')->with('success', 'Data Alternatif berhasil disimpan.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $alternative = Alternative::findOrFail($id);

        $request->validate([
            'criteria' => 'required|array',
            'criteria.*' => 'required|numeric|exists:sub_criterias,id',
        ]);

        AlternativeValue::where('alternative_id', $id)->delete();

        foreach ($request->criteria as $subCriteriaId) {
            $sub = SubCriteria::find($subCriteriaId);

            AlternativeValue::create([
                'alternative_id'    => $alternative->id,
                'sub_criteria_id'   => $subCriteriaId,
                'value'             => $sub->value ?? 0,
            ]);
        }

        return redirect()->route('admin.alternative.index')->with('success', 'Data Alternatif berhasil diubah.');
    }

    public function destroy($id): RedirectResponse
    {
        $alternative = Alternative::findOrFail($id);

        $alternative->delete();

        return redirect()->route('admin.alternative.index')->with('success', 'Data Alternatif berhasil dihapus.');
    }
}
