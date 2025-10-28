<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCriteria;
use App\Models\Criteria;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubCriteriaController extends Controller
{
    public function index()
    {
        $criteria = Criteria::with('subCriteria')->orderBy('code')->get();
        return view('admin.sub_criteria.index', compact('criteria'));
    }

    public function create(Request $request)
    {
        $criteria = Criteria::findOrFail($request->criteria_id);
        return view('admin.sub_criteria.create', compact('criteria'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'criteria_id' => 'required|exists:criterias,id',
            'name' => 'required|string|max:255',
            'value' => 'required|integer|min:1',
        ]);

        SubCriteria::create($request->all());

        return redirect()->route('admin.subcriteria.index')->with('success', 'Sub Kriteria berhasil ditambahkan.');
    }

    public function show($id)
    {
        $subCriteria = SubCriteria::findOrFail($id);
        return view('admin.sub_criteria.show', compact('subCriteria'));
    }

    public function edit($id)
    {
        $subCriteria = SubCriteria::findorfail($id);
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
            if ($e->errorInfo[1] == 10062) {
                return back()->withInput()->with('error', 'Kode sudah digunakan, gunakan kode lain.');
            }

            return back()->withInput()->with('error', 'Terjadi kesalahan, coba lagi.');
        }
    }

    public function destroy($id)
    {
        $criteria = SubCriteria::findorfail($id);
        $criteria->delete();

        return redirect()->route('admin.subcriteria.index')->with('success', 'Data berhasil dihapus');
    }
}
