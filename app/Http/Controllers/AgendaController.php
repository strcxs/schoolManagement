<?php

namespace App\Http\Controllers;

use App\Models\guru\Guru;
use App\Models\Kelas\Kelas;
use App\Models\Siswa\Siswa;
use Auth;
use Illuminate\Http\Request;
use App\Models\agenda\Agenda;
use App\Models\Absensi\absensi;
use Illuminate\Support\Facades\DB;

class AgendaController extends Controller
{
    private $data;

    public function index(){
        $id_guru = Auth::user()->id_guru; 
        if (Auth::user()->role->nama === "admin") {
            $this->data['agenda'] = Agenda::with('kelas')
                    ->with('guru')
                    ->get();
            $this->data['gurus'] = Guru::with('mapel')->get();
            $this->data['kelass'] = Kelas::get();
        } else{
            $this->data['agenda'] = Agenda::with('kelas')
                    ->with('guru')
                    ->where('id_guru','=',$id_guru)
                    ->get();
        }
        return view('agenda.index', $this->data);
    }

    public function absensi($data){
        $decoded_data = base64_decode($data);
        $decoded_data = json_decode($decoded_data, true);
        
        $id_kelas = $decoded_data['id_kelas']; 
        $id_agenda = $decoded_data['id_agenda'];

        $this->data['agenda'] = Agenda::where('id_kelas','=',$id_kelas)->where('id','=',$id_agenda)->first();
        $this->data['siswa'] = Siswa::with('kelas')->where('id_kelas','=',$id_kelas)->get();
        $this->data['absensi'] = absensi::where('id_agenda','=',$id_agenda)->get();
        return view('agenda.absensi', $this->data);
    }

    public function save(Request $request)
    {
        DB::beginTransaction();
        try {
            absensi::where('id_agenda','=',$request->get('id_agenda'))->delete();
            foreach ($request->get('kehadiranData') as $data) {
                $dataObj = (object)$data;
                if (isset($dataObj->sakit)) {
                    $absensi = new absensi();
                    $absensi->id_agenda = $request->get('id_agenda');
                    $absensi->sakit = $dataObj->NIS; 
                    $absensi->save();
                }
                if (isset($dataObj->izin)) {
                    $absensi = new absensi();
                    $absensi->id_agenda = $request->get('id_agenda');
                    $absensi->izin = $dataObj->NIS;
                    $absensi->save();
                }
                if (isset($dataObj->tidak_hadir)) {
                    $absensi = new absensi();
                    $absensi->id_agenda = $request->get('id_agenda');
                    $absensi->tidak_hadir = $dataObj->NIS;
                    $absensi->save();
                }
            }
            $agenda = Agenda::find($request->get('id_agenda'));
            $agenda->status = 1;
            $agenda->save();
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Absensi berhasil',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
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
