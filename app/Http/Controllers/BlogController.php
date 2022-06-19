<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Blog;
use App\Http\Controllers\AuthUserTrait;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
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
        return Blog::with('user:id,username')->get();
        // $data = Blog::all();
        // return response()->json($data);
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
        $fileName = time().$request->file('foto')->getClientOriginalName();
        $path = $request->file('foto')->storeAs('uploads/blogs', $fileName);
        $user->blogs()->create([
            'slug' => Str::slug(request('title'), '-') . '-' . time(),
            'judul'=>$request->judul,
            'foto'=>$path,
            'deskripsi' => request('deskripsi'),
            'author' => request('author'),
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
    public function show($id)
    {
        $data = Blog::find($id);
        return response()->json($data);
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
        $validation = $request->validate([
            'judul' => 'required|min:5',
            'foto' => '',
            'deskripsi' => 'required',
            'author' => 'required'
        ]);
        $blog = Blog::find($id);
        $this->checkOwnership($blog->user_id);
        if($request->file('foto')){
            $fileName = time().$request->file('foto')->getClientOriginalName();
            $path = $request->file('foto')->storeAs('uploads/blogs', $fileName);
            $validation['foto']=$path;
        }
        $validation['slug']=Str::slug(request('title'), '-') . '-' . time();
        $validation['user_id']=$blog->user_id;
        $blog->update($validation);
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
        $blog = Blog::find($id);

        //sama seperti check ownership di update func
        $this->checkOwnership($blog->user_id);

        $blog->delete();
        return response()->json([
            'code' => 200,
            'message' => 'Forum successfully deleted'
        ]);
    }

    private function validateRequest()
    {
        $validator = Validator::make(request()->all(), [
            'judul' => 'required|min:5',
            'foto' => 'required|file|mimes:png,jpg',
            'deskripsi' => 'required',
            'author' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->messages())->send();
            exit;
        }
    }
}
