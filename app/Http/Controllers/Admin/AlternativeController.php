<?php

namespace App\Http\Controllers\Admin;

use App\Models\Criteria;
use App\Models\Alternative;
use App\Models\AlternativeValue;
use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\SubCriteria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlternativeController extends Controller
{
    public function index(): View
    {
        $criterias = Criteria::with('subCriteria')->orderBy('id')->get(); // Ambil semua kriteria

        $alternatives = Alternative::with(['values.subCriteria.criteria', 'student'])->get();

        $dataAlternatives = $alternatives->map(function ($alt) use ($criterias) {
            $data = [
                'id' => $alt->id,
                'name' => $alt->car?->name
            ];

            foreach ($criterias as $criteria) {
                $value = $alt->values->first(function ($val) use ($criteria) {
                    return $val->subCriteria && $val->subCriteria->criteria_id === $criteria->id;
                });

                $data[$criteria->id] = $value && $value->subCriteria
                    ? $value->subCriteria->name
                    : '-';
            }

            return $data;
        });

        return view('admin.alternative.index', [
            'criterias' => $criterias,
            'alternatives' => $dataAlternatives,
        ]);
    }

    public function create(): View
    {
        $cars = Car::all();
        $criteria = Criteria::with('subCriteria')->orderBy('id', 'asc')->get();
        return view('admin.alternative.create', compact('criteria', 'students'));
    }

    public function show($id)
    {
        $alternative = Alternative::with([
            'values.subCriteria.criteria',
            'student'
        ])->findOrFail($id);

        return view('admin.alternative.show', compact('alternative'));
    }

    public function edit($id): View
    {
        $cars = Car::all();
        $alternative = Alternative::findOrFail($id);

        $criteria = Criteria::with('subCriteria')->orderBy('id', 'asc')->get();

        $selectedSubs = AlternativeValue::with('subCriteria')
            ->where('alternative_id', $id)
            ->get()
            ->mapWithKeys(function ($val) {
                return [$val->subCriteria->criteria_id => $val->sub_criteria_id];
            });

        return view('admin.alternative.edit', compact('alternative', 'criteria', 'selectedSubs', 'students'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'car_id' => 'required|numeric|exists:cars,id',
            'criteria' => 'required|array',
            'criteria.*' => 'required|numeric|exists:sub_criterias,id',
        ]);

        $alternative = Alternative::create([
            'car_id' => $request->car_id,
        ]);

        foreach ($request->criteria as $subCriteriaId) {
            $sub = SubCriteria::with('criteria')->find($subCriteriaId);

            AlternativeValue::create([
                'alternative_id'   => $alternative->id,
                'sub_criteria_id'  => $subCriteriaId,
                'value'            => $sub->value ?? 0,
            ]);
        }

        return redirect()->route('admin.alternative.index')->with('success', 'Data berhasil disimpan');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $alternative = Alternative::findOrFail($id);

        $request->validate([
            'car_id' => 'required|numeric|exists:cars,id',
            'criteria' => 'required|array',
            'criteria.*' => 'required|numeric|exists:sub_criterias,id',
        ]);

        $alternative->update(['car_id' => $request->car_id]);

        AlternativeValue::where('alternative_id', $id)->delete();

        foreach ($request->criteria as $subCriteriaId) {
            $sub = SubCriteria::with('criteria')->find($subCriteriaId);

            AlternativeValue::create([
                'alternative_id'   => $alternative->id,
                'sub_criteria_id'  => $subCriteriaId,
                'value'            => $sub->value ?? 0,
            ]);
        }

        return redirect()->route('admin.alternative.index')->with('success', 'Data berhasil diubah');
    }

    public function destroy($id): RedirectResponse
    {
        $alternative = Alternative::findOrFail($id);

        AlternativeValue::where('alternative_id', $id)->delete();
        $alternative->delete();

        return redirect()->route('admin.alternative.index')->with('success', 'Data berhasil dihapus');
    }
}
