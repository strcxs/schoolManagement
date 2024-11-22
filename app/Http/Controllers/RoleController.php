<?php

namespace App\Http\Controllers;

use App\Models\role\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    private $data;
    public function index(){
        $this->data['role'] = Role::get();
        return view('Role.index', $this->data);
    }
    public function edit(Request $request){
        // Menemukan data berdasarkan ID
        DB::beginTransaction();
        try {
            $update = Role::find($request->id);
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
}
