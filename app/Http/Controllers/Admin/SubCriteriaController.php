<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCriteria;
use App\Models\Criteria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubCriteriaController extends Controller
{
    public function index(): View
    {
        $criteriaCollection = Criteria::with([
            'subCriteria' => function ($query) {
                $query->orderBy('value', 'desc');
            },
            'major',
            'subject'
        ])
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

    public function store(Request $request)
    {
        $request->validate([
            'criteria_id' => 'required|exists:criterias,id',
            'name' => 'nullable|string|max:100',
            'value' => 'required|numeric|min:1|max:10',
            'min_value' => 'required|numeric|min:0',
            'max_value' => 'required|numeric|gt:min_value',
        ]);

        $criteriaId = $request->criteria_id;
        $name = $request->name ?? "Skala Nilai SPK: {$request->value}";

        try {

            $isExistingValue = SubCriteria::where('criteria_id', $criteriaId)
                ->where('value', $request->value)
                ->exists();

            if ($isExistingValue) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan. Nilai SPK (' . $request->value . ') sudah ada untuk kriteria ini.');
            }

            $isOverlap = SubCriteria::where('criteria_id', $criteriaId)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('min_value', [$request->min_value, $request->max_value])
                        ->orWhereBetween('max_value', [$request->min_value, $request->max_value])
                        ->orWhere(function ($query) use ($request) {
                            $query->where('min_value', '<', $request->min_value)
                                ->where('max_value', '>', $request->max_value);
                        });
                })->exists();

            if ($isOverlap) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan. Rentang Nilai Rapor (' . $request->min_value . '-' . $request->max_value . ') tumpang tindih dengan rentang yang sudah ada.');
            }

            SubCriteria::create([
                'criteria_id' => $criteriaId,
                'name' => $name,
                'value' => $request->value,
                'min_value' => $request->min_value,
                'max_value' => $request->max_value,
            ]);

            return redirect()->route('admin.subcriteria.index')
                ->with('success', "Skala Konversi '{$name}' (Nilai Rapor: {$request->min_value}-{$request->max_value}) berhasil ditambahkan.");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan Skala Konversi. Error: ' . $e->getMessage());
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

    public function update(Request $request, SubCriteria $sub_criterion)
    {
        $request->validate([
            'name' => 'nullable|string|max:100',
            'value' => [
                'required',
                'numeric',
                'min:1',
                'max:10',

                Rule::unique('sub_criterias')
                    ->ignore($sub_criterion->id)
                    ->where(fn($query) => $query->where('criteria_id', $sub_criterion->criteria_id))
            ],
            'min_value' => 'required|numeric|min:0',
            'max_value' => 'required|numeric|gt:min_value',
        ]);

        $criteriaId = $sub_criterion->criteria_id;
        $name = $request->name ?? "Skala Nilai SPK: {$request->value}";

        try {
            $isOverlap = SubCriteria::where('criteria_id', $criteriaId)
                ->where('id', '!=', $sub_criterion->id)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('min_value', [$request->min_value, $request->max_value])
                        ->orWhereBetween('max_value', [$request->min_value, $request->max_value])
                        ->orWhere(function ($query) use ($request) {
                            $query->where('min_value', '<', $request->min_value)
                                ->where('max_value', '>', $request->max_value);
                        });
                })->exists();

            if ($isOverlap) {
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui. Rentang Nilai Rapor (' . $request->min_value . '-' . $request->max_value . ') tumpang tindih dengan rentang lain yang sudah ada.');
            }

            $sub_criterion->update([
                'name' => $name,
                'value' => $request->value,
                'min_value' => $request->min_value,
                'max_value' => $request->max_value,
            ]);

            return redirect()->route('admin.subcriteria.index')
                ->with('success', 'Skala Konversi berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui Skala Nilai: ' . $e->getMessage());
        }
    }

    public function destroy($id): RedirectResponse
    {
        $subCriteria = SubCriteria::findorfail($id);

        if ($subCriteria->alternativeValues()->exists()) {
            return back()->with('error', 'Gagal menghapus. Skala Konversi ini sedang digunakan dalam data nilai alternatif siswa.');
        }

        $subCriteria->delete();

        return redirect()->route('admin.subcriteria.index')->with('success', 'Data berhasil dihapus');
    }
}
