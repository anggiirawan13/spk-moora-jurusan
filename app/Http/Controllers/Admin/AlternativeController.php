<?php

namespace App\Http\Controllers\Admin;

use App\Models\Criteria;
use App\Models\Alternative;
use App\Models\AlternativeValue;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SubCriteria;
use App\Models\Major; // Import Model Major
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AlternativeController extends Controller
{
    // ====================================================================
    // 🎯 INDEX: Menampilkan Daftar Alternatif
    // ====================================================================
    public function index(): View
    {
        // Tetap menggunakan Criteria dan Alternative untuk menampilkan tabel
        $criterias = Criteria::with(['major', 'subject'])->orderBy('id')->get();

        $alternatives = Alternative::with([
            'values.subCriteria.criteria',
            'student.major'
        ])->get();

        $dataAlternatives = $alternatives->map(function ($alt) use ($criterias) {

            $student = $alt->student;

            $data = [
                'id'         => $alt->id,
                'name'       => optional($student)->name ?? 'Siswa Tidak Ditemukan',
                'nis'        => optional($student)->nis ?? '-',
                'major_name' => optional(optional($student)->major)->name ?? '-',
            ];

            return $data;
        });

        return view('admin.alternative.index', [
            'criterias'      => $criterias,
            'alternatives' => $dataAlternatives,
        ]);
    }

    // ====================================================================
    // 🎯 CREATE: Mengambil SEMUA Jurusan, Kriteria, dan Sub Kriteria
    // ====================================================================
    public function create(): View
    {
        $existingAlternativeStudentIds = Alternative::pluck('student_id');
        $students = Student::whereNotIn('id', $existingAlternativeStudentIds)->get();

        // Mengambil SEMUA Jurusan beserta relasi Kriteria dan Sub Kriteria
        $majorsWithCriteria = Major::with([
            'criteria.subject',
            'criteria.subCriteria'
        ])->get();

        return view('admin.alternative.create', [
            'students' => $students,
            'majorsWithCriteria' => $majorsWithCriteria,
        ]);
    }

    // ====================================================================
    // 🎯 STORE: Menyimpan Data Sub Kriteria yang Dipilih (Mass Insertion)
    // ====================================================================
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'student_id' => 'required|numeric|unique:alternatives,student_id|exists:students,id',
            // Input dari View adalah: name="criteria[KriteriaID]" -> nilainya adalah SubCriteria ID
            'criteria' => 'required|array',
            'criteria.*' => 'required|numeric|exists:sub_criterias,id',
        ]);

        $alternative = Alternative::create([
            'student_id' => $request->student_id,
        ]);

        // Persiapkan data untuk Mass Insertion
        $alternativeValuesToCreate = [];
        if ($request->has('criteria')) {
            // Ambil semua SubCriteria ID yang dipilih oleh pengguna dari semua Kriteria
            $subCriteriaIds = array_values($request->criteria);

            // Query satu kali untuk mengambil semua data SubCriteria yang dipilih (Efisiensi)
            $selectedSubCriterias = SubCriteria::whereIn('id', $subCriteriaIds)->get()->keyBy('id');
            $now = Carbon::now();

            foreach ($subCriteriaIds as $subCriteriaId) {
                $sub = $selectedSubCriterias->get($subCriteriaId);

                if ($sub) {
                    $alternativeValuesToCreate[] = [
                        'alternative_id'    => $alternative->id,
                        'sub_criteria_id'   => $subCriteriaId,
                        'value'             => $sub->value ?? 0,
                        'created_at'        => $now,
                        'updated_at'        => $now,
                    ];
                }
            }
        }

        // Simpan semua dalam satu query (Mass Insertion)
        if (!empty($alternativeValuesToCreate)) {
            AlternativeValue::insert($alternativeValuesToCreate);
        }

        return redirect()->route('admin.alternative.index')->with('success', 'Data Alternatif berhasil disimpan.');
    }

    // ====================================================================
    // 🎯 EDIT: Mengambil SEMUA Jurusan dan Nilai yang Sudah Dipilih
    // ====================================================================
    public function edit(Alternative $alternative): View
    {
        $alternative->load('student');

        // Peta Sub Criteria ID yang sudah dipilih, dikelompokkan berdasarkan Criteria ID
        $selectedSubs = $alternative->values
            ->pluck('sub_criteria_id', 'subCriteria.criteria_id')
            ->toArray();

        // Mengambil SEMUA Jurusan beserta relasi Kriteria dan Sub Kriteria untuk input
        $majorsWithCriteria = Major::with([
            'criteria.subject',
            'criteria.subCriteria'
        ])->get();

        return view('admin.alternative.edit', [
            'alternative' => $alternative,
            'majorsWithCriteria' => $majorsWithCriteria,
            'selectedSubs' => $selectedSubs,
        ]);
    }

    // ====================================================================
    // 🎯 UPDATE: Memperbarui Data Sub Kriteria yang Dipilih (Mass Insertion)
    // ====================================================================
    public function update(Request $request, Alternative $alternative): RedirectResponse
    {
        $request->validate([
            'criteria' => 'required|array',
            'criteria.*' => 'required|numeric|exists:sub_criterias,id',
        ]);

        try {
            // Hapus nilai lama
            AlternativeValue::where('alternative_id', $alternative->id)->delete();

            // Persiapkan data untuk Mass Insertion
            $alternativeValuesToCreate = [];
            if ($request->has('criteria')) {
                // Ambil semua SubCriteria ID yang dipilih oleh pengguna
                $subCriteriaIds = array_values($request->criteria);

                // Query satu kali untuk mengambil semua data SubCriteria yang dipilih (Efisiensi)
                $selectedSubCriterias = SubCriteria::whereIn('id', $subCriteriaIds)->get()->keyBy('id');
                $now = Carbon::now();

                foreach ($subCriteriaIds as $subCriteriaId) {
                    $sub = $selectedSubCriterias->get($subCriteriaId);

                    if ($sub) {
                        $alternativeValuesToCreate[] = [
                            'alternative_id'    => $alternative->id,
                            'sub_criteria_id'   => $subCriteriaId,
                            'value'             => $sub->value ?? 0,
                            'created_at'        => $now,
                            'updated_at'        => $now,
                        ];
                    }
                }
            }

            // Simpan semua dalam satu query (Mass Insertion)
            if (!empty($alternativeValuesToCreate)) {
                AlternativeValue::insert($alternativeValuesToCreate);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui Data Alternatif: ' . $e->getMessage());
        }

        return redirect()->route('admin.alternative.index')->with('success', 'Data Alternatif berhasil diubah.');
    }

    // ====================================================================
    // 🎯 SHOW: Menampilkan Detail Alternatif
    // ====================================================================
    public function show(Alternative $alternative): View
    {
        $alternative->load([
            'student.major',
            'values.subCriteria.criteria.major',
            'values.subCriteria.criteria.subject',
        ]);

        // Mengelompokkan nilai berdasarkan Kriteria ID
        // Ini mengasumsikan Kriteria ID unik untuk setiap mata pelajaran yang dinilai.
        $groupedValues = $alternative->values
            ->groupBy('subCriteria.criteria.id');

        $uniqueCriteriaValues = collect();
        foreach ($groupedValues as $criteriaId => $valuesGroup) {
            $value = $valuesGroup->first();

            $uniqueCriteriaValues->push([
                'criteria'          => $value->subCriteria->criteria, // Mengandung subject, major, attribute_type
                'sub_criteria_name' => $value->subCriteria->name,
                'value_spk'         => $value->subCriteria->value,
            ]);
        }

        return view('admin.alternative.show', [
            'alternative' => $alternative,
            'uniqueCriteriaValues' => $uniqueCriteriaValues,
        ]);
    }

    // ====================================================================
    // 🎯 DESTROY: Menghapus Alternatif
    // ====================================================================
    public function destroy($id): RedirectResponse
    {
        $alternative = Alternative::findOrFail($id);
        $alternative->delete();
        return redirect()->route('admin.alternative.index')->with('success', 'Data Alternatif berhasil dihapus.');
    }
}
