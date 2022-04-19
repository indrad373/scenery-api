<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Http\Controllers\AuthUserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{

    use AuthUserTrait;

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
        return Forum::with('user:id,username', 'comments.user:id,username')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateRequest();

        $user = $this->getAuthUser();

        $user->forums()->create([
            'title' => request('title'),
            'body' => request('body'),
            'category' => request('category'),
            'slug' => Str::slug(request('title'), '-') . '-' . time(),
        ]);

        //response berhasil
        return response()->json([
            'code' => 200,
            'message' => 'Successfully posted'
        ]);
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
        return Forum::with('user:id,username', 'comments.user:id,username')->find($id);
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
        $this->validateRequest();

        //check ownership (authorize) untuk memastikan user mana yang mempunyai hak untuk update data forum yang telah dibuat sebelumnya
        $forum = Forum::find($id);

        //check user id dari auth user, apakah authorize atau tidak di AuthUserTrait
        $this->checkOwnership($forum->user_id);

        $forum->update([
            'title' => request('title'),
            'body' => request('body'),
            'category' => request('category'),
        ]);

        //response berhasil
        return response()->json([
            'code' => 200,
            'message' => 'Successfully updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //check ownership (authorize) untuk memastikan user mana yang mempunyai hak untuk hapus data forum yang telah dibuat sebelumnya
        $forum = Forum::find($id);
        
        //sama seperti check ownership di update func 
        $this->checkOwnership($forum->user_id);

        $forum->delete();
        return response()->json([
            'code' => 200,
            'message' => 'Forum successfully deleted'
        ]);
    }

    private function validateRequest()
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'required|min:5',
            'body' => 'required|min:10',
            'category' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->messages())->send();
            exit;
        }
    }
}
