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
        DB::beginTransaction();
        try {
            $update = Kelas::find($request->id);
            if ($update) {
                $update->nama = $request->nama;
                $update->save();
                DB::commit();
                return response()->json(['status'=>200, 'message' => 'Data berhasil diperbarui','data' => $update]);
            } else {
                DB::rollBack();
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }   
    }

    public function add(Request $request){
        // Menambahkan data kelas baru
        DB::beginTransaction();
        try {
            $kelas = new Kelas();
            $kelas->nama = $request->nama;
            $kelas->save();
            DB::commit();
            return response()->json(['status'=>200, 'message' => 'Data kelas berhasil ditambahkan', 'data' => $kelas]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request){
        // Menghapus data kelas
        DB::beginTransaction();
        try {
            $kelas = Kelas::find($request->id);
            if ($kelas) {
                $kelas->delete();
                DB::commit();
                return response()->json(['status'=>200, 'message' => 'Data kelas berhasil dihapus']);
            } else {
                DB::rollBack();
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
