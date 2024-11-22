<?php

namespace App\Http\Controllers;

use App\Models\mapel\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapelController extends Controller
{
    private $data;
    public function index(){
        $this->data['mapel'] = Mapel::get();
        return view('mapel.index', $this->data);
    }
    public function edit(Request $request){
        // Menemukan data berdasarkan ID
        DB::beginTransaction();
        try {
            $update = Mapel::find($request->id);
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
        // Menambahkan data mapel baru
        DB::beginTransaction();
        try {
            $mapel = new Mapel();
            $mapel->nama = $request->nama;
            $mapel->save();
            DB::commit();
            return response()->json(['status'=>200, 'message' => 'Data mapel berhasil ditambahkan', 'data' => $mapel]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request){
        // Menghapus data mapel berdasarkan ID
        DB::beginTransaction();
        try {
            $mapel = Mapel::find($request->id);
            if ($mapel) {
                $mapel->delete();
                DB::commit();
                return response()->json(['status'=>200, 'message' => 'Data mapel berhasil dihapus']);
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
