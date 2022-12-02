<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiFormatter;
use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $pgw = Pegawai::all();

        if ($pgw){
            return ApiFormatter::createApi(200,'Success',$pgw);
        }else{
            return ApiFormatter::createApi(404,'Data Not Found');
        }
    }

    public function byEmail($email)
    {

        $pgw = null;
        //check jika username yang dimasukan
        if (strpos($email,'@gmail.com') === false) { //check jika bukan gmail
            $email = strpos($email,'@bpkh.go.id') === false ? $email.'@bpkh.go.id': $email; //memastikan dengan email @bpkh.go.id
            $pgw = Pegawai::where('username',$email)->first(); //mendapatkan data pegawai dari user@bpkh.go.id
            if ($pgw === null) { //Jika gagal / kosong
                $email = explode('@',$email)[0]; //mengambil nama user
                $pgw = Pegawai::where('username', "$email@gmail.com")->first(); //mencoba dengan gmail
            }
        }else{
            $pgw = Pegawai::where('username',$email)->first(); //mencoba dengan email yang lain
        }
        
        if ($pgw){
            return ApiFormatter::createApi(200,'Success',$pgw);
        }else{
            return ApiFormatter::createApi(404,"$email Not Found");
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
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
