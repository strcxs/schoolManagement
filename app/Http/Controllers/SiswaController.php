<?php

namespace App\Http\Controllers;

use App\Models\Siswa\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    private $data;
    public function index(){
        $this->data['siswa'] = Siswa::with('kelas')->get();
        return view('siswa.index', $this->data);
    }
    public function edit(Request $request)
    {
        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Temukan siswa berdasarkan ID
            $siswa = Siswa::find($request->id);

            // Periksa apakah siswa ditemukan
            if ($siswa) {
                // Perbarui nama siswa dan field lainnya
                $siswa->nama = $request->nama;
                $siswa->id_kelas = $request->kelas;  // Asumsikan id_kelas diperbarui
                $siswa->save(); // Simpan perubahan

                // Commit transaksi
                DB::commit();

                // Kembalikan respon sukses
                return response()->json([
                    'status' => 200,
                    'message' => 'Data berhasil diperbarui',
                    'data' => $siswa
                ]);
            } else {
                // Rollback jika siswa tidak ditemukan
                DB::rollBack();
                return response()->json([
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
