<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\agenda\Agenda;
use Illuminate\Support\Facades\DB;

class AgendaController extends Controller
{
    private $data;
    public function index(){
        $this->data['agenda'] = Agenda::with('kelas')
                                ->with('guru')
                                ->get();
        return view('agenda.index', $this->data);
    }
    public function edit(Request $request)
    {
        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Temukan agenda berdasarkan ID
            $agenda = Agenda::find($request->id);

            // Periksa apakah agenda ditemukan
            if ($agenda) {
                // Perbarui data agenda
                $agenda->id_guru = $request->input('guru');
                $agenda->id_kelas = $request->input('kelas');
                $agenda->time_start = $request->input('time_start');
                $agenda->time_end = $request->input('time_end');
                $agenda->save(); // Simpan perubahan

                // Commit transaksi
                DB::commit();

                // Kembalikan respon sukses
                return response()->json([
                    'status' => 200,
                    'message' => 'Agenda berhasil diperbarui',
                    'data' => $agenda
                ]);
            } else {
                // Rollback jika agenda tidak ditemukan
                DB::rollBack();
                return response()->json([
                    'message' => 'Agenda tidak ditemukan'
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
