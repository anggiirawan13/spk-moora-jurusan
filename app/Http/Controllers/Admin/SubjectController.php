<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::all();
        return view('admin.subject.index', compact('subjects'));
    }

    public function create()
    {
        return view('admin.subject.create');
    }

    public function show($id)
    {
        $subject = Subject::findOrFail($id);
        return view('admin.subject.show', compact('subject'));
    }

    public function edit($id)
    {
        $subject = Subject::findOrFail($id);
        return view('admin.subject.edit', compact('subject'));
    }

    public function store(Request $request)
    {
        try {
            Subject::create([
                'code' => $request->code,
                'name' => $request->name,
            ]);

            return redirect()->route('admin.subject.index')->with('success', 'Data berhasil disimpan');
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
            $subject = [
                'code' => $request->code,
                'name' => $request->name,
            ];

            Subject::whereId($id)->update($subject);

            return redirect()->route('admin.subject.index')->with('success', 'Data berhasil diubah');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return back()->withInput()->with('error', 'Kode sudah digunakan, gunakan kode lain.');
            }

            return back()->withInput()->with('error', 'Terjadi kesalahan, coba lagi.');
        }
    }

    public function destroy($id)
    {
        $subject = Subject::findorfail($id);
        $subject->delete();

        return redirect()->route('admin.subject.index')->with('success', 'Data berhasil dihapus');
    }
}
