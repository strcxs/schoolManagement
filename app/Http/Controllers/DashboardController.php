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
                // \DB::enableQueryLog();
                $update->save();
                // dd(\DB::getQueryLog());
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
}
