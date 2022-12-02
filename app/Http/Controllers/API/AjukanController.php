<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Str;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Models\Ajukan;
use App\Models\Kendaraan;
use App\Models\Lampiran;
use App\Models\Pengemudi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AjukanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ajukan = Ajukan::with('lampiran')->get();

        if ($ajukan){
            return ApiFormatter::createApi(200,'Success',$ajukan);
        }else{
            return ApiFormatter::createApi(404,'Data Not Found');
        }
    }

    public function ajuanKu($username)
    {
        if (strpos($username,'@bpkh.go.id')) {
            $username = explode('@',$username)[0];
        }
        $check = Ajukan::where('user',"$username")->first();
        
        if ($check) {
            $limit = request()->query('limit',10);
            $q = request()->query('q','');
            $ajukan = Ajukan::where('user',"$username")
                            ->where(function($query) use ($q){
                                $query->where('tujuan','LIKE','%'.$q.'%')->orWhere('keterangan','LIKE','%'.$q.'%');                    
                            })->paginate($limit);

            if ($ajukan){
                return ApiFormatter::createApi(200,"Success mendapat data pengajuan dari : $username",$ajukan);
            }else{
                return ApiFormatter::createApi(404,'Data Not Found');
            }
        }else{
            return ApiFormatter::createApi(404,'Data Not Found');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $ajukan = new Ajukan();
        $ajukan->id_aju = (string) Str::uuid();
        $ajukan->tw_create = Carbon::now()->toDateTimeString();
        $ajukan->tw_update = Carbon::now()->toDateTimeString();
        $ajukan->jenis = $request->jenis;
        $ajukan->waktu_pakai_mulai = $request->waktu_pakai_mulai;
        $ajukan->waktu_pakai_selesai = $request->waktu_pakai_selesai;
        $ajukan->tujuan = $request->tujuan;
        $ajukan->keterangan = $request->keterangan;
        $ajukan->jml_penumpang = (string) isset($request->jml_penumpang) ? implode(',',(array)$request->jml_penumpang) : null;
        $ajukan->user = $request->user;
        $ajukan->status = 1;
        
        //$ajukan->unit = $request->unit;
        //$ajukan->kendaraan = $request->kendaraan;
        //$ajukan->pengemudi = $request->pengemudi;
        //$ajukan->penjelasan = $request->waktu_pakai_mulai;
        //$ajukan->kode_konfirm = ;
        //$ajukan->esign = ;
        //$ajukan->upload_esign = ;
        //$ajukan->waktu_esign = ;
        $ajukan->save();

        if ($ajukan){

            if($request->file !== null) {

                $now = Carbon::now();
                $file = $request->file;
                $name_file = $file->getClientOriginalName();
                $ext = $file->extension();
                $name_new = md5($name_file.$now->format('Y_m_d_H_i_s')).'.'.$ext;
                $upload_path = 'uploads/'.$now->format('Y').'/'.$now->format('m').'/'.$now->format('d');
                $upload = $file->move(public_path($upload_path), $name_new);
    
                if ($upload) {
                    $lampiran = new Lampiran();
                    $lampiran->id = (string) Str::uuid();
                    $lampiran->id_aju = $ajukan->id_aju;
                    $lampiran->nama_file = $name_file;
                    $lampiran->new_name = $name_new;
                    $lampiran->ext = $ext;
                    $lampiran->jenis = 'LAMPIRAN';
                    $lampiran->path = $upload_path;
                    $lampiran->save();

                    $data = [
                        'ajukan' => $ajukan,
                        'lampiran' => $lampiran,
                    ];

                    return ApiFormatter::createApi(200,"Berhasil Menambah data dengan Upload File : $name_file",$data);
                }else{
                    return ApiFormatter::createApi(409,'Gagal Mengupload File');
                }
                
            }else{
                return ApiFormatter::createApi(200,'Berhasil menambah data tanpa File Lampiran', $ajukan);
            }
        }else{
            return ApiFormatter::createApi(409,'Gagal menambah data');
        }   
    }

    public function approved(Request $request, $id)
    {
        $status = [0,1];
        $ajukan = Ajukan::where('id_aju',$id)->with(['pengemudi','lampiran','kendaraan'])->first();
        if (in_array($ajukan->status,$status)) {
            $ajukan->status = 2;
            $ajukan->pengemudi = $request->pengemudi;
            $ajukan->kendaraan = $request->kendaraan;
            $ajukan->save();

            if ($ajukan){
                $now = Carbon::now();
                if ($now->format('dmY') === date('dmY',strtotime($ajukan->waktu_pakai_mulai))) {
                    $pengemudi = Pengemudi::where('id',$request->pengemudi)->first();
                    $pengemudi->stats = 1;
                    $pengemudi->stats_label = 'TUGAS';
                    $pengemudi->save();

                    $kendaraan = Kendaraan::where('id',$request->kendaraan)->first();
                    $kendaraan->status_pinjam = 1;
                    $kendaraan->pengemudi = $request->pengemudi;
                    $kendaraan->id_aju = $ajukan->id_aju;
                    $kendaraan->user_pinjam = $ajukan->user;
                    $kendaraan->update();

                    return ApiFormatter::createApi(200,"Success mensetujui data pengajuan : $id dan menugaskan pengemudi",$ajukan);
                }

                return ApiFormatter::createApi(200,"Success mensetujui data pengajuan : $id",$ajukan);
            }else{
                return ApiFormatter::createApi(404,'Data Not Found');
            }
        }else{
            $label_status = self::status($ajukan->status);
            return ApiFormatter::createApi(404,'Tidak dapat disetujui karena status telah '.$label_status."($ajukan->status)");
        }
    }

    public function reject(Request $request, $id)
    {
        $status = [0,1];
        $ajukan = Ajukan::where('id_aju',$id)->first();
        if (in_array($ajukan->status,$status)) {
            $ajukan->status = 4;
            $ajukan->penjelasan = isset($request->penjelasan) ? $request->penjelasan : null;
            $ajukan->update();

            if ($ajukan){
                return ApiFormatter::createApi(200,"Success menolak data pengajuan : $id",$ajukan);
            }else{
                return ApiFormatter::createApi(404,'Gagal Mengupdate : '.$id);
            }
        }else{
            $label_status = self::status($ajukan->status);
            return ApiFormatter::createApi(404,'Tidak dapat ditolak karena status telah '.$label_status."($ajukan->status)");
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function lihat($id)
    {
        $ajukan = Ajukan::where('id_aju',$id)->with(['pengemudi','lampiran'])->first();
        if ($ajukan) {
            return ApiFormatter::createApi(200,"Success mendapat data pengajuan : $id",$ajukan);
        }else{
            return ApiFormatter::createApi(404,'Data not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $status = [0,1,2];
        $ajukan = Ajukan::where('id_aju',$id)->first();
        if (in_array($ajukan->status,$status)) {
            $ajukan->tw_update = Carbon::now()->toDateTimeString();
            $ajukan->status = isset($request->status) ? $request->status : $ajukan->status;
            $ajukan->jenis = isset($request->jenis) ? $request->jenis : $ajukan->jenis;
            $ajukan->waktu_pakai_mulai = isset($request->waktu_pakai_mulai) ? $request->waktu_pakai_mulai : $ajukan->waktu_pakai_mulai;
            $ajukan->waktu_pakai_selesai = isset($request->waktu_pakai_selesai) ? $request->waktu_pakai_selesai : $ajukan->waktu_pakai_selesai;
            $ajukan->tujuan = isset($request->tujuan) ? $request->tujuan : $ajukan->tujuan;
            $ajukan->keterangan = isset($request->keterangan) ? $request->keterangan : $ajukan->keterangan;
            $ajukan->jml_penumpang = (string) isset($request->jml_penumpang) ? implode(',',(array)$request->jml_penumpang) : $ajukan->jml_penumpang;
            
            //$ajukan->unit = $request->unit;
            //$ajukan->kendaraan = $request->kendaraan;
            //$ajukan->pengemudi = $request->pengemudi;
            //$ajukan->penjelasan = $request->waktu_pakai_mulai;
            //$ajukan->kode_konfirm = ;
            //$ajukan->esign = ;
            //$ajukan->upload_esign = ;
            //$ajukan->waktu_esign = ;
            $ajukan->update();

            if ($ajukan){

                if($request->file !== null) {

                    $lampiran = $ajukan->lampiran;
                    $path_old = public_path($lampiran->path.'/'.$lampiran->new_name);
                    
                    if (file_exists($path_old)) {
                        unlink($path_old);
                    }
                    
                    $now = Carbon::now();
                    $file = $request->file;
                    $name_file = $file->getClientOriginalName();
                    $ext = $file->extension();
                    $name_new = md5($name_file.$now->format('Y_m_d_H_i_s')).'.'.$ext;
                    $upload_path = 'uploads/'.$now->format('Y').'/'.$now->format('m').'/'.$now->format('d');
                    $upload = $file->move(public_path($upload_path), $name_new);

                    if ($upload) {
                        
                        $lampiran->nama_file = $name_file;
                        $lampiran->new_name = $name_new;
                        $lampiran->ext = $ext;
                        $lampiran->path = $upload_path;
                        $lampiran->update();

                        return ApiFormatter::createApi(200,"Berhasil mengubah data dengan Upload File : $name_file , old file : $path_old, new file : $lampiran->path/$lampiran->new_name",$ajukan);
                    }else{
                        return ApiFormatter::createApi(409,'Gagal Mengupload File');
                    }
                    
                }else{
                    return ApiFormatter::createApi(200,'Berhasil mengubah data tanpa File Lampiran', $ajukan);
                }
            }else{
                return ApiFormatter::createApi(409,'Gagal mengubah data');
            }
        }else{
            $label_status = self::status($ajukan->status);
            return ApiFormatter::createApi(409,'Tidak dapat mengubah, karena status telah '.$label_status);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ajukan = Ajukan::where('id_aju',$id)->first();
        if ($ajukan) {
            $ajukan->delete();
            return ApiFormatter::createApi(200,"Success menghapus data pengajuan : $id",$ajukan);
        }else{
            return ApiFormatter::createApi(404,'Data not found');
        }
    }

    public function status($status)
    {
        $label_status = null;
        switch ($status){
            case 2:
                $label_status = "Disetujui";
                break;
            case 3:
                $label_status = "Dibatalkan";
                break;
            case 4:
                $label_status = "Ditolak";
                break;
            case 5:
                $label_status = "Selesai";
                break;
        }

        return $label_status;
    }

}
