<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Str;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kendaraan = Kendaraan::all();

        if ($kendaraan){
            return ApiFormatter::createApi(200,"Success mendapat data kendaraan",$kendaraan);
        }else{
            return ApiFormatter::createApi(404,'Data Not Found');
        }
    }

    public function byStatus($status)
    {
        $kendaraan = Kendaraan::where('status_pinjam',$status)->get();

        if ($kendaraan){
            return ApiFormatter::createApi(200,"Success mendapat data kendaraan",$kendaraan);
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
        $kendaraan = new Kendaraan();
        $kendaraan->id = (string) Str::uuid();
        $kendaraan->nopol = $request->nopol;
        $kendaraan->kondisi = $request->kondisi;
        $kendaraan->status_pinjam = 0;
        $kendaraan->nama = $request->nama;
        $kendaraan->save();

        if ($kendaraan){
            return ApiFormatter::createApi(200,"Success menambah data kendaraan",$kendaraan);
        }else{
            return ApiFormatter::createApi(404,'Data Not Found');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $kendaraan = Kendaraan::where('id',$id)->first();
        
        if ($kendaraan){
            return ApiFormatter::createApi(200,"Success mendapat data kendaraan dengan id = $id",$kendaraan);
        }else{
            return ApiFormatter::createApi(404,'Data Not Found');
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
        $kendaraan = Kendaraan::where('id',"$id")->first();
        $kendaraan->nopol = isset($request->nopol) ? $request->nopol : $kendaraan->nopol;
        $kendaraan->kondisi = isset($request->kondisi) ? $request->kondisi : $kendaraan->kondisi;
        $kendaraan->status_pinjam = isset($request->status_pinjam) ? $request->status_pinjam : $kendaraan->status_pinjam;
        $kendaraan->nama = isset($request->nama) ? $request->nama : $kendaraan->nama;
        $kendaraan->update();

        if ($kendaraan){
            return ApiFormatter::createApi(200,"Success mengupdate data kendaraan dengan id = $id",$kendaraan);
        }else{
            return ApiFormatter::createApi(404,'Data Not Found');
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
        //
    }
}
