<?php

namespace App\Http\Controllers;
use App\Http\Controllers\AuthUserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForumCommentController extends Controller
{
    use AuthUserTrait;

    public function __construct()
    {
        return auth()->shouldUse('api');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $forumId)
    {
        $this->validateRequest();

        $user = $this->getAuthUser();

        $user->forumComments()->create([
            'body' => request('body'),
            'forum_id' => $forumId
        ]);

        //response berhasil
        return response()->json([
            'code' => 200,
            'message' => 'Comment successfully posted'
        ]);
    }

    private function validateRequest()
    {
        $validator = Validator::make(request()->all(), [
            'body' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->messages())->send();
            exit;
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
