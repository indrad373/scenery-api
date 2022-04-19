<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AuthUserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ForumComment;

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

        if ($validator->fails()) {
            return response()->json($validator->messages())->send();
            exit;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $forumId, $commentId)
    {
        $this->validateRequest();

        //check ownership (authorize) untuk memastikan user mana yang mempunyai hak untuk update data forum yang telah dibuat sebelumnya
        $forumComment = ForumComment::find($commentId);

        //check user id dari auth user, apakah authorize atau tidak di AuthUserTrait
        $this->checkOwnership($forumComment->user_id);

        $forumComment->update([
            'body' => request('body'),
        ]);

        //response berhasil
        return response()->json([
            'code' => 200,
            'message' => 'Comment successfully updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($forumId, $commentId)
    {
        //check ownership (authorize) untuk memastikan user mana yang mempunyai hak untuk hapus data comment yang telah dibuat sebelumnya
        $forumComment = ForumComment::find($commentId);

        //sama seperti check ownership di update func 
        $this->checkOwnership($forumComment->user_id);

        $forumComment->delete();
        return response()->json([
            'code' => 200,
            'message' => 'Comment successfully deleted'
        ]);
    }
}
