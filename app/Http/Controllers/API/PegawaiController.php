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
        if (strpos($email,'@gmail.com') === false) {
            $email = strpos($email,'@bpkh.go.id') === false ? $email.'@bpkh.go.id': $email;
            $pgw = Pegawai::where('username',$email)->first();
            if ($pgw === null) {
                $email = explode('@',$email)[0];
                $pgw = Pegawai::where('username', "$email@gmail.com")->first();
            }
        }else{
            $pgw = Pegawai::where('username',$email)->first();
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
