<?php

namespace App\Http\Controllers;

use App\Models\mapel\Mapel;
use App\Models\User;
use App\Models\guru\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    private $data;
    
    public function index(){
        $this->data['guru'] = Guru::with('mapel')->get();
        $this->data['mapels'] = Mapel::get();
        return view('guru.index', $this->data);
    }

    public function add(Request $request)
    {
        DB::beginTransaction();

        try {
            $guru = new Guru();
            $guru->NIP = $request->nip;
            $guru->nama = $request->nama;
            $guru->id_mapel = $request->mapel;
            $guru->save();

            $user = new User();
            $user->id_guru = $guru->id;
            $user->id_role = 2;
            $user->username = $guru->NIP;
            $user->password = Hash::make($guru->NIP);
            $user->save();

            $guru = Guru::with('mapel')->find($guru->id);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data berhasil ditambahkan',
                'data' => $guru
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
            User::where('id_guru','=',$request->id)->delete();
            $guru = Guru::find($request->id);

            if ($guru) {
                $guru->delete();
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

    public function edit(Request $request)
    {
        DB::beginTransaction();

        try {
            $guru = Guru::find($request->id);

            if ($guru) {
                $guru->nama = $request->nama;
                $guru->id_mapel = $request->mapel;
                $guru->save();
                $guru = Guru::with('mapel')->find($guru->id);
                DB::commit();

                return response()->json([
                    'status' => 200,
                    'message' => 'Data berhasil diperbarui',
                    'data' => $guru
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
