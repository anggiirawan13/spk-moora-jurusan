<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCriteria;
use App\Models\Criteria;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubCriteriaController extends Controller
{
    public function index(): View
    {
        $criteriaCollection = Criteria::with(['subCriteria', 'major', 'subject'])
            ->orderBy('major_id', 'asc')
            ->orderBy('subject_id', 'asc')
            ->get();

        $groupedByMajor = $criteriaCollection->groupBy('major_id');

        $majors = collect();
        foreach ($groupedByMajor as $majorId => $criteriaGroup) {
            $major = $criteriaGroup->first()->major;

            $major->criteria = $criteriaGroup;

            $majors->push($major);
        }

        return view('admin.sub_criteria.index', compact('majors'));
    }

    public function create(Request $request): View | RedirectResponse
    {
        if (!$request->criteria_id) {
            return redirect()->route('admin.criteria.index')->with('error', 'Pilih Kriteria (Mata Pelajaran) terlebih dahulu.');
        }

        $criteria = Criteria::with(['major', 'subject'])->findOrFail($request->criteria_id);

        return view('admin.sub_criteria.create', compact('criteria'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'criteria_id' => 'required|exists:criterias,id',
            'name' => 'required|string|max:255',
            'value' => 'required|integer|min:1',
        ]);

        try {
            SubCriteria::create($request->only(['criteria_id', 'name', 'value']));

            return redirect()->route('admin.subcriteria.index')->with('success', 'Sub Kriteria berhasil ditambahkan.');
        } catch (QueryException $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. Coba lagi.');
        }
    }

    public function show($id): View
    {
        $subCriteria = SubCriteria::with('criteria.major', 'criteria.subject')->findOrFail($id);
        return view('admin.sub_criteria.show', compact('subCriteria'));
    }

    public function edit($id): View
    {
        $subCriteria = SubCriteria::with('criteria.major', 'criteria.subject')->findOrFail($id);
        return view('admin.sub_criteria.edit', compact('subCriteria'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'criteria_id' => 'required|exists:criterias,id',
            'name' => 'required|string|max:255',
            'value' => 'required|integer|min:1',
        ]);

        try {
            $subCriteria = [
                'name' => $request->name,
                'value' => $request->value
            ];

            SubCriteria::whereId($id)->update($subCriteria);

            return redirect()->route('admin.subcriteria.index')->with('success', 'Data berhasil diubah');
        } catch (QueryException $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. Coba lagi.');
        }
    }

    public function destroy($id): RedirectResponse
    {
        $subCriteria = SubCriteria::findorfail($id);
        $subCriteria->delete();

        return redirect()->route('admin.subcriteria.index')->with('success', 'Data berhasil dihapus');
    }
}
