<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Str;
use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Models\Pengemudi;
use Illuminate\Http\Request;

class PengemudiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pengemudi = Pengemudi::all();

        if ($pengemudi){
            return ApiFormatter::createApi(200,"Success mendapat data pengemudi",$pengemudi);
        }else{
            return ApiFormatter::createApi(404,'Data Not Found');
        }
    }

    public function byStatus($status)
    {
        $pengemudi = Pengemudi::where('stats',$status)->get();

        if ($pengemudi){
            return ApiFormatter::createApi(200,"Success mendapat data pengemudi",$pengemudi);
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
        $pengemudi = new Pengemudi();
        $pengemudi->id = (string) Str::uuid();
        $pengemudi->nip = $request->nip;
        $pengemudi->nama = $request->nama;
        $pengemudi->email = $request->email;
        $pengemudi->stats = $request->stats;
        $pengemudi->stats_label = $request->stats_label;
        $pengemudi->save();

        if ($pengemudi){
            return ApiFormatter::createApi(200,"Success menambah data pengemudi",$pengemudi);
        }else{
            return ApiFormatter::createApi(409,'Gagal menambah pengemudi');
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
        $pengemudi = Pengemudi::where('id',$id)->first();

        if ($pengemudi){
            return ApiFormatter::createApi(200,"Success mendapat data pengemudi dengan id : $id",$pengemudi);
        }else{
            return ApiFormatter::createApi(409,'Gagal menambah pengemudi');
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
        $pengemudi = Pengemudi::where('id',$id)->first();
        $pengemudi->id = (string) Str::uuid();
        $pengemudi->nip = isset($request->nip) ? $request->nip : $pengemudi->nip;
        $pengemudi->nama = isset($request->nama) ? $request->nama : $pengemudi->nama;
        $pengemudi->email = isset($request->email) ? $request->email : $pengemudi->email;
        $pengemudi->stats = isset($request->stats) ? $request->stats : $pengemudi->stats;
        $pengemudi->stats_label = isset($request->stats_label) ? $request->stats_label : $pengemudi->stats_label;
        $pengemudi->update();

        if ($pengemudi){
            return ApiFormatter::createApi(200,"Success mengubah data pengemudi",$pengemudi);
        }else{
            return ApiFormatter::createApi(409,'Gagal mengubah pengemudi',$request);
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
    }
}
