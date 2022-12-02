<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\Ajukan;
use App\Models\Kendaraan;
use App\Models\Pegawai;
use App\Models\Pengemudi;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->checkDriver();
    }

    public function checkDriver()
    {
        $now = Carbon::now();
        $berhasil = [];
        $pengajuan = Ajukan::where('status',2)->with('pengemudi')->get();
        foreach ($pengajuan as $aju) {
            if ($now->format('dmY') === date('dmY',strtotime($aju->waktu_pakai_mulai))) {
                if (isset($aju->pengemudi) && $aju->pengemudi !== null) {
                    $pengemudi = Pengemudi::where('id',$aju->pengemudi)->first();
                    $pengemudi->stats = 1;
                    $pengemudi->stats_label = 'TUGAS';
                    $pengemudi->update();
                    
                    $kendaraan = Kendaraan::where('id',$aju->kendaraan)->first();
                    $kendaraan->status_pinjam = 1;
                    $kendaraan->pengemudi = $aju->pengemudi;
                    $kendaraan->id_aju = $aju->id_aju;
                    $kendaraan->user_pinjam = $aju->user;
                    $kendaraan->update();

                    $berhasil[] = [
                        'id_aju'    => $aju->id_aju,
                        'tujuan'    => $aju->tujuan,
                        'keterangan'=> $aju->keterangan,
                        'id_driver' => $pengemudi->id,
                        'driver'    => $pengemudi->nama,
                        'status'    => $pengemudi->stats,
                        'label'     => $pengemudi->stats_label,
                        'waktu_mulai'   => $aju->waktu_pakai_mulai,
                        'waktu_selesai'   => $aju->waktu_pakai_selesai,
                        'kendaraan' => $kendaraan ? 'berhasil diupdate pinjam' : 'gagal diupdate pinjam',
                    ];
                }
            }
        }
    }

}
