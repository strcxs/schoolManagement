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

    public function add(Request $request)
    {
        DB::beginTransaction();

        try {
            $agenda = new Agenda();
            $agenda->id_guru = $request->input('guru');
            $agenda->id_kelas = $request->input('kelas');
            $agenda->time_start = $request->input('time_start');
            $agenda->time_end = $request->input('time_end');
            $agenda->save();
            
            $agenda = Agenda::with('kelas')
            ->with('guru')
            ->find($agenda->id);
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Agenda berhasil ditambahkan',
                'data' => $agenda
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
            $agenda = Agenda::find($request->id);

            if ($agenda) {
                $agenda->delete();
                DB::commit();

                return response()->json([
                    'status' => 200,
                    'message' => 'Agenda berhasil dihapus'
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'Agenda tidak ditemukan'
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
            $agenda = Agenda::find($request->id);

            if ($agenda) {
                $agenda->id_guru = $request->input('guru');
                $agenda->id_kelas = $request->input('kelas');
                $agenda->time_start = $request->input('time_start');
                $agenda->time_end = $request->input('time_end');
                $agenda->save();

                $agenda = Agenda::with('kelas')
                ->with('guru')
                ->find($agenda->id);
                
                DB::commit();

                return response()->json([
                    'status' => 200,
                    'message' => 'Agenda berhasil diperbarui',
                    'data' => $agenda
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'Agenda tidak ditemukan'
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
