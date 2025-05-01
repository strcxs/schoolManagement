<?php

namespace App\Http\Controllers;

use App\Models\mapel\Mapel;
use Auth;
use App\Models\guru\Guru;
use Illuminate\Http\Request;
use App\Models\agenda\Agenda;
use App\Models\Schedule\Scheduler;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    private $data;

    public function index(){
        if (Auth::user()->role->nama === "admin") {
            $this->data['schedule'] = Scheduler::with('guru')
                    ->with('mapel')
                    ->with('agenda')
                    ->get();
            $this->data['mapels'] = Mapel::get();
            // $this->data['kelass'] = Kelas::get();
        }
        return view('schedule.index', $this->data);
    }
    public function editSchedule($data){
        $id_mapel = Scheduler::select('id_mapel')->where('id',$data)->first()->id_mapel;
        $this->data['schedule_id'] = $data;
        $this->data['gurus'] = Guru::get();
        $this->data['id_guruSelected'] = Scheduler::select('id_guru')->where('id',$data)->first()->id_guru;
        $this->data['mapel'] = Scheduler::where('id',$data)->with('mapel')->first();
        if (Auth::user()->role->nama === "admin") {
            $this->data['agenda'] = Agenda::where('id_mapel', $id_mapel)
            ->where(function ($query) use ($data) {
                $query->whereNull('id_schedule')
                    ->orWhere('id_schedule', $data);
            })
            ->get();
        }
        // dd($this->data);
        return view('schedule.editPage', $this->data);
    }
    public function save(Request $request)
    {
        DB::beginTransaction();
        try {
            Agenda::where('id_schedule',$request->get('id_schedule'))->update(['id_schedule' => NULL]);;
            foreach ($request->get('kehadiranData') as $data) {
                $dataObj = (object)$data;
                if (isset($dataObj->scheduleAdd)) {
                    $agenda = Agenda::find($dataObj->id_agenda);
                    $agenda->id_schedule = $request->get('id_schedule');
                    $agenda->save();
                }
                $schedule = Scheduler::find($request->get('id_schedule'));
                $schedule->id_guru = $request->get('id_guru');
                $schedule->save();
            }
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Set Schedule berhasil',
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
            $schedule = Scheduler::find($request->id);

            if ($schedule) {
                $schedule->delete();
                DB::commit();

                return response()->json([
                    'status' => 200,
                    'message' => 'Schedule berhasil dihapus'
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'Schedule tidak ditemukan'
                ], 404);
            }
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
            $guru = new Scheduler();
            $guru->nama_schedule = $request->nama_schedule;
            $guru->id_mapel = $request->mapel;
            $guru->save();

            $guru = Scheduler::with(['mapel','guru','agenda'])->find($guru->id);

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
}
