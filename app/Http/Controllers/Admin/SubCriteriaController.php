<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCriteria;
use App\Models\Criteria;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

    public function store(Request $request)
    {
        $request->validate([
            'criteria_id' => 'required|exists:criterias,id',
            // Validasi bahwa minimal satu checkbox dipilih
            'sub_criteria_to_add' => 'required|array|min:1',
        ]);

        $criteriaId = $request->criteria_id;
        $selectedSubs = $request->sub_criteria_to_add;

        // Hardcode data yang tersedia
        $fixedSubCriteria = [
            5 => 'Sangat Baik',
            4 => 'Baik',
            3 => 'Cukup',
            2 => 'Kurang',
            1 => 'Sangat Kurang',
        ];

        try {
            $count = 0;
            foreach ($selectedSubs as $value => $isChecked) {
                // Pastikan nilai value ada di fixedSubCriteria (untuk keamanan)
                if (array_key_exists($value, $fixedSubCriteria)) {
                    // Cek duplikasi di DB (nilai $value adalah Nilai SPK)
                    $isExisting = SubCriteria::where('criteria_id', $criteriaId)
                        ->where('value', $value)
                        ->exists();

                    if (!$isExisting) {
                        SubCriteria::create([
                            'criteria_id' => $criteriaId,
                            'name' => $fixedSubCriteria[$value],
                            'value' => $value,
                        ]);
                        $count++;
                    }
                }
            }

            if ($count > 0) {
                return redirect()->route('admin.subcriteria.index', $criteriaId)
                    ->with('success', "$count Skala Nilai berhasil ditambahkan ke kriteria.");
            }

            return redirect()->route('admin.subcriteria.index', $criteriaId)
                ->with('info', "Tidak ada Skala Nilai baru yang ditambahkan.");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan Skala Nilai. Error: ' . $e->getMessage());
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
            // ... rules
            // Gunakan $sub_criterion->id dalam ignore
            Rule::unique('sub_criterias')->ignore($sub_criterion->id)->where(function ($query) use ($request) {
                return $query->where('criteria_id', $request->criteria_id);
            }),
        ]);

        try {
            // Lakukan update data pada $sub_criterion
            $sub_criterion->update([
                'name' => $request->name,
                'value' => $request->value,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui Skala Nilai: ' . $e->getMessage());
        }

        // Menggunakan nama route yang benar: 'subcriteria.index'
        return redirect()->route('admin.subcriteria.index', $request->criteria_id)
            ->with('success', 'Skala Nilai berhasil diperbarui.');
    }

    public function destroy($id): RedirectResponse
    {
        $subCriteria = SubCriteria::findorfail($id);
        $subCriteria->delete();

        return redirect()->route('admin.subcriteria.index')->with('success', 'Data berhasil dihapus');
    }
}
