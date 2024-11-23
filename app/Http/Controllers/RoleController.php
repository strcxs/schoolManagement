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

    public function add(Request $request){
        DB::beginTransaction();
        try {
            $role = new Role();
            $role->nama = $request->nama;
            $role->save();
            DB::commit();
            return response()->json(['status'=>200, 'message' => 'Role berhasil ditambahkan', 'data' => $role]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function edit(Request $request){
        DB::beginTransaction();
        try {
            $update = Role::find($request->id);
            if ($update) {
                $update->nama = $request->nama;
                $update->save();
                DB::commit();
                return response()->json(['status'=>200, 'message' => 'Data berhasil diperbarui', 'data' => $update]);
            } else {
                DB::rollBack();
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }   
    }

    public function delete(Request $request){
        DB::beginTransaction();
        try {
            $role = Role::find($request->id);
            if ($role) {
                $role->delete();
                DB::commit();
                return response()->json(['status'=>200, 'message' => 'Role berhasil dihapus']);
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
