<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Criteria;
use App\Models\Major;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Database\QueryException;

class CriteriaController extends Controller
{
    public function index(): View
    {
        $majors = Major::with(['criteria.subject'])->get();

        return view('admin.criteria.index', compact('majors'));
    }

    public function create(): View
    {
        $majors = Major::orderBy('name', 'asc')->get();
        $subjects = Subject::orderBy('name', 'asc')->get();

        return view('admin.criteria.create', compact('majors', 'subjects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'major_id'       => 'required|exists:majors,id',
            'subject_id'     => 'required|exists:subjects,id|unique:criterias,subject_id,NULL,id,major_id,' . $request->major_id,
            'weight'         => 'required|numeric|min:0.01|max:1',
            'attribute_type' => 'required|in:Benefit,Cost'
        ], [
            'subject_id.unique' => 'Mata pelajaran ini sudah dijadikan kriteria untuk jurusan ini.',
        ]);

        try {
            Criteria::create([
                'major_id'       => $request->major_id,
                'subject_id'     => $request->subject_id,
                'weight'         => $request->weight,
                'attribute_type' => $request->attribute_type,
            ]);

            return redirect()->route('admin.criteria.index')->with('success', 'Kriteria berhasil disimpan.');
        } catch (QueryException $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. Pastikan bobot total tidak melebihi 1.0.');
        }
    }

    public function show($id): View
    {
        $criteria = Criteria::with(['major', 'subject'])->findOrFail($id);

        return view('admin.criteria.show', compact('criteria'));
    }

    public function edit($id): View
    {
        $criteria = Criteria::findOrFail($id);
        $majors = Major::orderBy('name', 'asc')->get();
        $subjects = Subject::orderBy('name', 'asc')->get();

        return view('admin.criteria.edit', compact('criteria', 'majors', 'subjects'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'major_id'       => 'required|exists:majors,id',
            'subject_id'     => 'required|exists:subjects,id|unique:criteria,subject_id,' . $id . ',id,major_id,' . $request->major_id,
            'weight'         => 'required|numeric|min:0.01|max:1',
            'attribute_type' => 'required|in:Benefit,Cost',
        ], [
            'subject_id.unique' => 'Mata pelajaran ini sudah dijadikan kriteria untuk jurusan ini.',
        ]);

        try {
            $criteria = [
                'major_id'       => $request->major_id,
                'subject_id'     => $request->subject_id,
                'weight'         => $request->weight,
                'attribute_type' => $request->attribute_type,
            ];

            Criteria::whereId($id)->update($criteria);

            return redirect()->route('admin.criteria.index')->with('success', 'Kriteria berhasil diubah.');
        } catch (QueryException $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function destroy($id): RedirectResponse
    {
        $criteria = Criteria::findorfail($id);

        $criteria->delete();

        return redirect()->route('admin.criteria.index')->with('success', 'Kriteria berhasil dihapus.');
    }
}
