<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{

    public function __construct()
    {
        return auth()->shouldUse('api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        /*
        * get all forum data
        *   return Forum::all();
        **/

        //get forum data with user who create the data eg: user with id 1, it will show details about user with id 1
        //return Forum::with('user')->get();

        //kita buat lebih simple lagi user data yang akan kita tampilkan
        return Forum::with('user:id,username')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validationAttribute($request);
        $user = $this->getAuthUser();
        

        $user->forums()->create([
            'title' => request('title'),
            'body' => request('body'),
            'category' => request('category'),
            'slug' => Str::slug(request('title'), '-') . '-' . time(),
        ]);

        //generate token, auto login, atau hanya response berhasil
        return response()->json(['message' => 'Successfully posted']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     //menampilkan salah 1 data
    public function show($id)
    {
        return Forum::with('user:id,username')->find($id);
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
        $validator = Validator::make($request->all(), $this->getValidationAttribute());

        if($validator->fails()){
            return response()->json($validator->messages());
        }

        $user = $this->getAuthUser();

        //check ownership (authorize) untuk memastikan user mana yang mempunyai hak untuk update data forum yang telah dibuat sebelumnya

        //ketika dapat id nya maka saya update forum tsb
        Forum::find($id)->update([
            'title' => request('title'),
            'body' => request('body'),
            'category' => request('category'),
        ]);

        //generate token, auto login, atau hanya response berhasil
        return response()->json(['message' => 'Successfully updated']);
    }

    private function getValidationAttribute()
    {
        return [
            'title' => 'required|min:5',
            'body' => 'required|min:10',
            'category' => 'required',
        ];
    }

    private function getAuthUser()
    {
        try {
            return auth()->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['message' => 'Unauthorized, anda harus login terlebih dahulu']);
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
