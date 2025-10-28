<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    public function index()
    {
        $majors = Major::all();
        return view('admin.major.index', compact('majors'));
    }

    public function create()
    {
        return view('admin.major.create');
    }

    public function show($id)
    {
        $major = Major::findOrFail($id);
        return view('admin.major.show', compact('major'));
    }

    public function edit($id)
    {
        $major = Major::findOrFail($id);
        return view('admin.major.edit', compact('major'));
    }

    public function store(Request $request)
    {
        try {
            Major::create([
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return redirect()->route('admin.major.index')->with('success', 'Data berhasil disimpan');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return back()->withInput()->with('error', 'Kode sudah digunakan, gunakan kode lain.');
            }

            return back()->withInput()->with('error', 'Terjadi kesalahan, coba lagi.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $major = [
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
            ];

            Major::whereId($id)->update($major);

            return redirect()->route('admin.major.index')->with('success', 'Data berhasil diubah');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return back()->withInput()->with('error', 'Kode sudah digunakan, gunakan kode lain.');
            }

            return back()->withInput()->with('error', 'Terjadi kesalahan, coba lagi.');
        }
    }

    public function destroy($id)
    {
        $major = Major::findorfail($id);
        $major->delete();

        return redirect()->route('admin.major.index')->with('success', 'Data berhasil dihapus');
    }
}
