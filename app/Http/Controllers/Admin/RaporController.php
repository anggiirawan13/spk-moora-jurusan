<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Major;
use App\Models\Alternative; // Diperlukan
use App\Models\AlternativeValue; // Diperlukan
use App\Models\RaporValue;
use App\Models\Criteria;
use App\Models\SubCriteria;
use Illuminate\Support\Facades\DB;

class RaporController extends Controller
{
    /**
     * READ (Index): Menampilkan daftar semua jurusan, termasuk yang belum ada siswanya, 
     * dan mengelompokkan siswa di bawah jurusan masing-masing.
     */
    public function index()
    {
        $majors = Major::with([
            'students' => fn($query) => $query->latest()
        ])->orderBy('name', 'asc')->get();

        $unassignedStudents = Student::whereNull('major_id')->latest()->get();
        $majorsData = [];

        // 1. Tambahkan Jurusan yang SUDAH Ada
        foreach ($majors as $major) {
            $majorsData[] = [
                'major_id' => $major->id,
                'major_name' => $major->name,
                'students' => $major->students->map(fn($student) => [
                    'id' => $student->id,
                    'nis' => $student->nis,
                    'name' => $student->name,
                    'current_major' => $major->name,
                ])->all(),
            ];
        }

        // 2. Tambahkan kategori 'Belum Dijuruskan'
        $majorsData[] = [
            'major_id' => 'null_major',
            'major_name' => 'Belum Dijuruskan',
            'students' => $unassignedStudents->map(fn($student) => [
                'id' => $student->id,
                'nis' => $student->nis,
                'name' => $student->name,
                'current_major' => 'Belum Dijuruskan',
            ])->all(),
        ];

        return view('admin.rapor.index', compact('majorsData'));
    }

    /**
     * READ/FORM (Show): Menampilkan form input/edit nilai rapor untuk siswa tertentu.
     */
    public function show(Student $student)
    {
        $student->load('major', 'alternative'); // Load alternative juga

        if (is_null($student->major_id)) {
            return redirect()->route('admin.rapor.index')->with('error', 'Siswa ' . $student->name . ' belum memiliki Jurusan yang ditetapkan. Tidak dapat menginput nilai rapor.');
        }

        // 1. Ambil Kriteria yang berlaku HANYA untuk Jurusan siswa.
        $criteria = Criteria::where('major_id', $student->major_id)
            ->with('subject')
            ->get();

        // 2. Ambil nilai rapor mentah. [subject_id => value]
        $raporValues = RaporValue::where('student_id', $student->id)->pluck('value', 'subject_id')->toArray();

        // 3. Ambil hasil konversi (Alternative Value) yang sudah tersimpan.
        $convertedValues = []; // [criteria_id => sub_criteria_id]

        if ($student->alternative) {
            $alternativeId = $student->alternative->id;

            // Ambil SubCriteria ID yang tersimpan untuk Alternative ini
            $subCriteriaIdsStored = AlternativeValue::where('alternative_id', $alternativeId)
                ->pluck('sub_criteria_id')
                ->toArray();

            // Lakukan mapping: cari subCriteria yang tersimpan dan cocokkan dengan criteria yang sedang di-loop
            foreach ($criteria as $c) {
                // Cari SubCriteria dari kriteria ini yang ID-nya ada di $subCriteriaIdsStored
                $storedSubCriteria = SubCriteria::where('criteria_id', $c->id)
                    ->whereIn('id', $subCriteriaIdsStored)
                    ->first();

                if ($storedSubCriteria) {
                    $convertedValues[$c->id] = $storedSubCriteria->id;
                }
            }
        }

        // 4. Ambil detail SPK untuk mapping tampilan status konversi.
        $subCriteriaIds = array_filter($convertedValues); // Harus array
        $spkDetails = !empty($subCriteriaIds) ? SubCriteria::whereIn('id', $subCriteriaIds)->get() : collect();

        $spkDetailsMap = $spkDetails->pluck('value', 'id')->toArray();   // Nilai SPK (C)
        $spkNamesMap = $spkDetails->pluck('name', 'id')->toArray();     // Nama Skala

        return view('admin.rapor.show', compact('student', 'criteria', 'raporValues', 'convertedValues', 'spkDetailsMap', 'spkNamesMap'));
    }

    /**
     * CREATE/UPDATE/DELETE (Store): Menyimpan atau mengupdate nilai rapor dan mengkonversinya.
     */
    public function update(Request $request, Student $student)
    {
        if (!$student || is_null($student->id)) {
            // Ini tidak boleh terjadi jika Route Model Binding berfungsi.
            return redirect()->route('admin.rapor.index')->with('error', 'Gagal memproses data siswa. ID siswa tidak ditemukan.');
        }
        // 1. Validasi
        $criteriaData = $request->input('criteria_data', []);
        $rules = [];
        foreach ($criteriaData as $criteriaId => $data) {
            $rules["values.{$criteriaId}"] = 'nullable|numeric|min:0|max:100';
        }

        $validatedData = $request->validate($rules, [
            'values.*.numeric' => 'Nilai rapor harus berupa angka.',
            'values.*.min' => 'Nilai rapor tidak boleh negatif.',
            'values.*.max' => 'Nilai rapor maksimal adalah 100.',
        ]);

        $inputValues = $validatedData['values'] ?? [];

        DB::beginTransaction();
        try {
            // Pastikan baris di tabel 'alternatives' sudah ada
            $alternative = Alternative::firstOrCreate(['student_id' => $student->id]);
            $alternativeId = $alternative->id;

            foreach ($criteriaData as $criteriaId => $data) {
                $originalValue = $inputValues[$criteriaId] ?? null;
                $subjectId = $data['subject_id'];
                $subCriteriaId = null;

                // --- BAGIAN 1: CRUD NILAI MENTAH (RAPOR VALUE) ---
                if (!is_null($originalValue)) {
                    // CREATE/UPDATE: Simpan nilai mentah
                    RaporValue::updateOrCreate(
                        ['student_id' => $student->id, 'subject_id' => $subjectId],
                        ['value' => $originalValue]
                    );

                    // Lakukan Konversi
                    $subCriteria = SubCriteria::where('criteria_id', $criteriaId)
                        ->where('min_value', '<=', $originalValue)
                        ->where('max_value', '>=', $originalValue)
                        ->first();

                    if ($subCriteria) {
                        $subCriteriaId = $subCriteria->id;
                    } else {
                        session()->flash('warning', 'Nilai ' . $originalValue . ' untuk Kriteria ID ' . $criteriaId . ' tidak masuk ke rentang skala konversi. Cek sub kriteria.');
                    }

                    // --- BAGIAN 2: CREATE/UPDATE HASIL KONVERSI (ALTERNATIVE VALUE) ---
                    if ($subCriteriaId) {
                        AlternativeValue::updateOrCreate(
                            ['alternative_id' => $alternativeId, 'sub_criteria_id' => $subCriteriaId],
                            ['value' => $subCriteria->value]
                        );
                    }
                } else {
                    // DELETE Implisit: Hapus RaporValue
                    RaporValue::where('student_id', $student->id)->where('subject_id', $subjectId)->delete();

                    // DELETE Implisit: Hapus AlternativeValue terkait
                    $subCriteriaIdsToDelete = SubCriteria::where('criteria_id', $criteriaId)->pluck('id');

                    AlternativeValue::where('alternative_id', $alternativeId)
                        ->whereIn('sub_criteria_id', $subCriteriaIdsToDelete)
                        ->delete();
                }
            }

            DB::commit();
            $message = session('warning') ? 'Nilai rapor berhasil disimpan, namun ada beberapa nilai yang tidak terkonversi.' : 'Nilai rapor siswa ' . $student->name . ' berhasil disimpan dan dikonversi.';

            return redirect()->route('admin.rapor.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data nilai rapor. Error: ' . $e->getMessage());
        }
    }

    public function convertValue(Request $request)
    {
        $request->validate([
            'criteria_id' => 'required|exists:criterias,id',
            'value' => 'nullable|numeric|min:0|max:100',
        ]);

        $criteriaId = $request->criteria_id;
        $value = $request->value;

        if (is_null($value)) {
            return response()->json([
                'spk_value' => 'N/A',
                'spk_name' => 'Belum Ada Nilai',
                'status_class' => 'badge-secondary text-danger', // Kelas CSS untuk badge
            ]);
        }

        // Lakukan Konversi: Cari SubCriteria yang rentang nilainya mencakup nilai rapor
        $subCriteria = SubCriteria::where('criteria_id', $criteriaId)
            ->where('min_value', '<=', $value)
            ->where('max_value', '>=', $value)
            ->first();

        if ($subCriteria) {
            return response()->json([
                'spk_value' => $subCriteria->value,
                'spk_name' => $subCriteria->name,
                'status_class' => 'badge-success',
            ]);
        }

        // Jika tidak ditemukan rentang konversi yang cocok
        return response()->json([
            'spk_value' => 'N/A',
            'spk_name' => 'Diluar Skala',
            'status_class' => 'badge-danger',
        ]);
    }
}
