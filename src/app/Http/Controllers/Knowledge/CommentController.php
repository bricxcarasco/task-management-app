<?php

namespace App\Http\Controllers\Knowledge;

use App\Http\Controllers\Controller;
use App\Http\Requests\Knowledge\CreateCommentRequest;
use App\Models\KnowledgeComment;
use DB;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    /**
     * Save Comment
     *
     * @param \App\Http\Requests\Knowledge\CreateCommentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCommentRequest $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        DB::beginTransaction();

        try {
            $comment = new KnowledgeComment();
            $comment->fill($request->validated());
            $comment->rio_id = $rio->id;
            $comment->save();

            DB::commit();

            return response()->respondSuccess();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            report($exception);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Delete Comment
     *
     * @param \App\Models\KnowledgeComment $knowledgeComment
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function destroy(KnowledgeComment $knowledgeComment)
    {
        $this->authorize('modifiable', [KnowledgeComment::class, $knowledgeComment]);

        DB::beginTransaction();

        try {
            $knowledgeComment->delete();

            DB::commit();

            return response()->respondSuccess();
        } catch (\Exception $exception) {
            DB::rollBack();

            Log::debug($exception);
            report($exception);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Update Comment
     *
     * @param \App\Http\Requests\Knowledge\CreateCommentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(KnowledgeComment $knowledgeComment, CreateCommentRequest $request)
    {
        $this->authorize('modifiable', [KnowledgeComment::class, $knowledgeComment]);

        /** @var \App\Models\User */
        $user = auth()->user();
        $requestData = $request->validated();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        DB::beginTransaction();

        try {
            $knowledgeComment->comment = $requestData['comment'];
            $knowledgeComment->save();

            DB::commit();

            return response()->respondSuccess();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            report($exception);

            return response()->respondInternalServerError();
        }
    }
}
