<?php

namespace App\Http\Controllers;

use App\Models\guru\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    private $data;
    public function index(){
        $this->data['guru'] = Guru::with('mapel')->get();
        return view('guru.index',$this->data);
    }
    public function edit(Request $request)
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Find the teacher (guru) by ID
            $guru = Guru::find($request->id);

            // Check if the teacher exists
            if ($guru) {
                // Update the teacher's name and other fields
                $guru->nama = $request->nama;
                $guru->id_mapel = $request->mapel;  // Assuming mapel_id is being updated
                $guru->save(); // Save the changes

                // Commit the transaction
                DB::commit();

                // Return a success response
                return response()->json([
                    'status' => 200,
                    'message' => 'Data berhasil diperbarui',
                    'data' => $guru
                ]);
            } else {
                // Rollback if the teacher is not found
                DB::rollBack();
                return response()->json([
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
