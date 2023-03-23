<?php

namespace App\Http\Controllers\Knowledge;

use App\Enums\Knowledge\Types;
use App\Enums\ServiceSelectionTypes;
use App\Http\Controllers\Controller;
use App\Http\Resources\Knowledge\ArticleResource;
use App\Http\Resources\Knowledge\FolderResource;
use App\Models\Knowledge;
use App\Objects\ServiceSelected;
use App\Http\Requests\Knowledge\CreateFolderRequest;
use App\Http\Requests\Knowledge\MoveKnowledgeRequest;
use App\Http\Requests\Knowledge\RenameFolderRequest;
use DB;
use Illuminate\Support\Facades\Log;
use Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class KnowledgeController extends Controller
{
    /**
     * Knowledge list page
     *
     * @param int $id Knowledge ID
     * @return \Illuminate\View\View
     */
    public function index($id = null)
    {
        if (!empty($id)) {
            $this->authorize('accessibleFolder', [Knowledge::class, $id]);
        }

        // Get knowledge entity
        $knowledge = Knowledge::whereId($id)->first();

        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        return view('knowledges.index', compact(
            'knowledge',
            'rio',
            'service',
        ));
    }

    /**
     * Fetch all public folder list
     *
     * @param int $id Knowledge ID
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function getFolders($id = null)
    {
        $knowledges = Knowledge::getPublicFolders($id)->get();

        return FolderResource::collection($knowledges);
    }

    /**
     * Fetch all public article list
     *
     * @param int $id Knowledge ID
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function getArticles($id = null)
    {
        $knowledges = Knowledge::getPublicArticles($id)->get();

        return ArticleResource::collection($knowledges);
    }

    /**
     * Create document folder.
     *
     * Endpoint: /api/knowledges/folder
     * Method: POST
     *
     * @param \App\Http\Requests\Knowledge\CreateFolderRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function createFolder(CreateFolderRequest $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get request data
        $requestData = $request->validated();

        DB::beginTransaction();

        try {
            // Create new instance of Document model
            $knowledge = new Knowledge();

            // Service selection session state values
            $service = ServiceSelected::getSelected();

            // Set create new folder data
            $knowledge->fill($requestData);
            $knowledge->type = Types::FOLDER;
            $knowledge->created_rio_id = $user->rio_id;

            if ($service->type === ServiceSelectionTypes::RIO) {
                $knowledge->owner_rio_id = $user->rio_id;
            }

            if ($service->type === ServiceSelectionTypes::NEO) {
                $knowledge->owner_neo_id = $service->data->id;
            }

            // Check if authorize to handle request
            $this->authorize('create', [Knowledge::class, $knowledge, $service]);

            // Save new folder
            $knowledge->save();

            DB::commit();

            return response()->respondSuccess();
        } catch (NotFoundHttpException $e) {
            report($e);
            DB::rollBack();

            Log::debug($e);
            return response()->respondNotFound();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::debug($e);
            report($e);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Rename document folder.
     *
     * @param \App\Http\Requests\Knowledge\RenameFolderRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function renameFolder(RenameFolderRequest $request, Knowledge $knowledge)
    {
        // Service selection session state values
        $service = ServiceSelected::getSelected();

        // Check if authorize to handle request
        $this->authorize('modifiable', [Knowledge::class, $knowledge, $service]);

        // Get request data
        $requestData = $request->validated();

        // Update task
        $knowledge->update($requestData);

        return response()->respondSuccess();
    }

    /**
    * move knowledge
    *
    * @param \App\Http\Requests\Knowledge\MoveKnowledgeRequest $request
    *
    * @return \Illuminate\Http\Response
    */
    public function moveKnowledge(MoveKnowledgeRequest $request, Knowledge $knowledge)
    {
        // Get current selected service
        $service = ServiceSelected::getSelected();

        // If destination is null destination folder is root
        $destinationFolder = null;

        // Check if user owns the destination folder
        if ($request->filled('directory_id')) {
            $destinationFolder = Knowledge::find($request->directory_id);
            $this->authorize('modifiable', [Knowledge::class, $destinationFolder, $service]);
        }

        // Check if user owns the knowledge to transfer
        $this->authorize('modifiable', [Knowledge::class, $knowledge, $service]);

        // Get request data
        $requestData = $request->validated();

        // Update task
        $knowledge->update($requestData);

        session()->put('alert', [
            'status' => 'success',
            'message' => __('Data has been moved'),
        ]);

        return response()->respondSuccess();
    }

    /**
     * Delete a public folder or article.
     *
     * @param \App\Models\Knowledge $knowledge
     * @return \Illuminate\Http\Response
     */
    public function delete(Knowledge $knowledge)
    {
        // Get current selected service
        $service = ServiceSelected::getSelected();

        // Check if authorized to handle request
        $this->authorize('modifiable', [Knowledge::class, $knowledge, $service]);

        DB::beginTransaction();

        try {
            // Delete article/folder including subfolders
            // and articles inside folder
            $knowledge->delete();

            DB::commit();

            if ($knowledge->type === Types::FOLDER) {
                // Set success flash message
                session()->put('alert', [
                    'status' => 'success',
                    'message' => __('Folder has been deleted'),
                ]);
            } else {
                // Set success flash message
                session()->put('alert', [
                    'status' => 'success',
                    'message' => __('Article has been deleted'),
                ]);
            }

            return response()->respondSuccess();
        } catch (\Exception $exception) {
            DB::rollBack();

            Log::debug($exception);
            report($exception);

            return response()->respondInternalServerError();
        }
    }
}
