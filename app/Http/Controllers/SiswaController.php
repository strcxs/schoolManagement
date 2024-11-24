<?php

namespace App\Http\Controllers;

use App\Models\Kelas\Kelas;
use App\Models\Siswa\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    private $data;

    public function index(){
        $this->data['siswa'] = Siswa::with('kelas')->get();
        $this->data['kelass'] = Kelas::get();
        return view('siswa.index', $this->data);
    }

    public function edit(Request $request)
    {
        DB::beginTransaction();

        try {
            $siswa = Siswa::find($request->id);

            if ($siswa) {
                $siswa->nama = $request->nama;
                $siswa->id_kelas = $request->kelas;
                $siswa->save();
                $siswa = Siswa::with('kelas')->find($siswa->id);
                DB::commit();

                return response()->json([
                    'status' => 200,
                    'message' => 'Data berhasil diperbarui',
                    'data' => $siswa
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function add(Request $request)
    {
        DB::beginTransaction();

        try {
            $siswa = new Siswa();
            $siswa->NIS = $request->nis;
            $siswa->nama = $request->nama;
            $siswa->id_kelas = $request->kelas;
            $siswa->save();
            $siswa = Siswa::with('kelas')->find($siswa->id);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data berhasil ditambahkan',
                'data' => $siswa
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();

        try {
            $siswa = Siswa::find($request->id);

            if ($siswa) {
                $siswa->delete();

                DB::commit();

                return response()->json([
                    'status' => 200,
                    'message' => 'Data berhasil dihapus'
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
