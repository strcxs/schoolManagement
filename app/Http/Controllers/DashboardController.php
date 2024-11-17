<?php

namespace App\Http\Controllers;

use App\Models\Kelas\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private $data;
    public function index(){
        $this->data['kelas'] = Kelas::get();
        return view('kelas.index', $this->data);
    }
    public function edit(Request $request){
        // Menemukan data berdasarkan ID
        $update = Kelas::find($request->id);

        DB::beginTransaction();
        try {
            // Pastikan data ditemukan
            if ($update) {
                // Mengupdate nilai kolom 'nama' dengan nilai baru
                $update->nama = $request->nama;
                
                // Pastikan data yang ingin diperbarui valid
                if ($update->isDirty()) {
                    // Menyimpan perubahan
                    $update->save();
                    DB::commit();
                    
                    // Menambahkan response atau redirect jika perlu
                    return response()->json(['status'=>200, 'message' => 'Data berhasil diperbarui','data' => $update]);
                } else {
                    DB::rollBack();
                    return response()->json(['message' => 'Tidak ada perubahan data'], 400);
                }
            } else {
                // Jika data tidak ditemukan
                DB::rollBack();
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }
        } catch (\Exception $e) {
            // Rollback dan log error jika terjadi exception
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan pada server'], 500);
        }
    }
    
}
