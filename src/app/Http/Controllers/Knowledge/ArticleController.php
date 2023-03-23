<?php

namespace App\Http\Controllers\Knowledge;

use App\Enums\Knowledge\ArticleTypes;
use App\Enums\Knowledge\Types;
use App\Enums\ServiceSelectionTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Knowledge\CreateArticleRequest;
use App\Http\Resources\Knowledge\CommentResource;
use App\Http\Requests\Knowledge\SearchArticlesRequest;
use App\Http\Requests\Knowledge\UpsertArticleDraftRequest;
use App\Http\Resources\Knowledge\ArticleResource;
use App\Models\Knowledge;
use App\Models\KnowledgeComment;
use App\Objects\ServiceSelected;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Session;

class ArticleController extends Controller
{
    /**
     * Article draft list
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        // Get article draft lists
        $articles = Knowledge::draftList()->get();

        $serviceName = $service->type === ServiceSelectionTypes::NEO
            ? $service->data->organization_name
            : $rio->full_name;

        return view('knowledges.articles.index', compact(
            'rio',
            'service',
            'articles',
            'serviceName'
        ));
    }

    /**
     * Create article page
     *
     * @param int $id Knowledge/Article ID
     * @return \Illuminate\View\View
     */
    public function create($id = null)
    {
        // Set initial values
        $knowledge = null;
        $directoryId = $id;

        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        // Determine if create article or draft continuation
        if ($id) {
            $knowledge = Knowledge::whereId($id)->firstOrFail();

            // Guard clause for knowledge type is folder
            switch ($knowledge->type) {
                case Types::FOLDER:
                    $this->authorize('accessibleFolder', [Knowledge::class, $id]);
                    $knowledge = null;
                    break;
                case Types::ARTICLE:
                    $this->authorize('accessibleDraft', [Knowledge::class, $knowledge, $service]);
                    $directoryId = $knowledge->directory_id;
                    break;
                default:
                    abort(404);
            }
        }

        return view('knowledges.articles.create', compact(
            'rio',
            'service',
            'directoryId',
            'knowledge'
        ));
    }

    /**
     * Save article
     *
     * @param \App\Http\Requests\Knowledge\CreateArticleRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateArticleRequest $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        DB::beginTransaction();

        try {
            $knowledge = new Knowledge();
            $knowledge->fill($request->formAttributes());
            $knowledge->created_rio_id = $rio->id;
            $knowledge->type = Types::ARTICLE;
            $knowledge->is_draft = ArticleTypes::PUBLIC;

            $referenceUrls = [];
            $requestData = $request->validated();
            //Get all input urls and convert to json
            foreach ($requestData as $key => $value) {
                $isReferenceUrl = preg_match('/reference/', strval($key));
                if ($isReferenceUrl) {
                    array_push($referenceUrls, $value);
                }
            }

            $knowledge->urls = strval(json_encode($referenceUrls, JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES));

            if ($service->type === ServiceSelectionTypes::RIO) {
                $knowledge->owner_rio_id = $user->rio_id;
            }
            if ($service->type === ServiceSelectionTypes::NEO) {
                $knowledge->owner_neo_id = $service->data->id;
            }

            // Check if authorize to handle request
            $this->authorize('create', [Knowledge::class, $knowledge, $service]);

            $knowledge->save();
            DB::commit();

            session()->put('alert', [
                'status' => 'success',
                'message' => __('Article created'),
            ]);

            return response()->respondSuccess();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            report($exception);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Update article
     * Update a draft into a public article
     *
     * @param \App\Http\Requests\Knowledge\CreateArticleRequest $request
     * @param  int $id Knowledge/Article ID
     * @return \Illuminate\Http\Response
     */
    public function update(CreateArticleRequest $request, int $id)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        $knowledge = Knowledge::whereId($id)->firstOrFail();
        // Guard clause for knowledge type is folder
        if ($knowledge->type === Types::FOLDER) {
            return response()->respondNotFound();
        }

        // Check authorization
        $this->authorize('accessibleDraft', [Knowledge::class, $knowledge, $service]);

        DB::beginTransaction();

        try {
            $knowledge->fill($request->formAttributes());
            $knowledge->created_rio_id = $rio->id;
            $knowledge->type = Types::ARTICLE;
            $knowledge->is_draft = ArticleTypes::PUBLIC;

            $referenceUrls = [];
            $requestData = $request->validated();
            //Get all input urls and convert to json
            foreach ($requestData as $key => $value) {
                $isReferenceUrl = preg_match('/reference/', strval($key));
                if ($isReferenceUrl && $value !== null) {
                    array_push($referenceUrls, $value);
                }
            }

            $knowledge->urls = strval(json_encode($referenceUrls, JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES));

            if ($service->type === ServiceSelectionTypes::RIO) {
                $knowledge->owner_rio_id = $user->rio_id;
            }
            if ($service->type === ServiceSelectionTypes::NEO) {
                $knowledge->owner_neo_id = $service->data->id;
            }

            $knowledge->save();
            DB::commit();

            $message = !$request->updatePublishedArticle ? 'Article created' : 'Article has been updated';
            session()->put('alert', [
                'status' => 'success',
                'message' => __($message),
            ]);

            return response()->respondSuccess();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            report($exception);

            session()->put('alert', [
                'status' => 'danger',
                'message' => __('Server Error'),
            ]);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Delete an article draft.
     *
     * @param \App\Models\Knowledge $knowledge
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Knowledge $knowledge)
    {
        try {
            DB::beginTransaction();

            // Get service selected
            $service = ServiceSelected::getSelected();

            // Guard clause if non-existing document
            if (empty($knowledge) || $knowledge->is_draft !== ArticleTypes::DRAFT || $knowledge->type === Types::FOLDER) {
                return response()->respondNotFound();
            }

            // Check authorization
            $this->authorize('modifiable', [Knowledge::class, $knowledge, $service]);
            $knowledge->delete();

            DB::commit();

            // Set success flash message
            session()->put('alert', [
                'status' => 'success',
                'message' => __('Draft article deleted'),
            ]);

            return response()->respondSuccess();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            // Set error flash message
            session()->put('alert', [
                'status' => 'danger',
                'message' => __('Server Error'),
            ]);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Save article as draft
     *
     * @param \App\Http\Requests\Knowledge\UpsertArticleDraftRequest $request
     * @return \Illuminate\Http\Response
     */
    public function createDraft(UpsertArticleDraftRequest $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        DB::beginTransaction();

        try {
            $knowledge = new Knowledge();
            $knowledge->fill($request->formAttributes());
            $knowledge->created_rio_id = $rio->id;
            $knowledge->type = Types::ARTICLE;
            $knowledge->is_draft = ArticleTypes::DRAFT;

            $referenceUrls = [];
            $requestData = $request->validated();
            // Get all input urls and convert to json
            foreach ($requestData as $key => $value) {
                $isReferenceUrl = preg_match('/reference/', strval($key));
                if ($isReferenceUrl) {
                    array_push($referenceUrls, $value);
                }
            }

            $knowledge->urls = strval(json_encode($referenceUrls, JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES));

            if ($service->type === ServiceSelectionTypes::RIO) {
                $knowledge->owner_rio_id = $user->rio_id;
            }
            if ($service->type === ServiceSelectionTypes::NEO) {
                $knowledge->owner_neo_id = $service->data->id;
            }

            // Check if authorize to handle request
            $this->authorize('accessibleDraft', [Knowledge::class, $knowledge, $service]);

            $knowledge->save();
            $knowledgeId = $knowledge->id;
            DB::commit();

            return response()->respondSuccess(['id' => $knowledgeId]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            report($exception);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Update article draft
     *
     * @param \App\Http\Requests\Knowledge\UpsertArticleDraftRequest $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function updateDraft(UpsertArticleDraftRequest $request, int $id)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        $knowledge = Knowledge::whereId($id)->firstOrfail();
        // Guard clause for non-existing knowledge
        if (
            $knowledge->is_draft !== ArticleTypes::DRAFT
            || $knowledge->type === Types::FOLDER
        ) {
            return response()->respondNotFound();
        }

        // Check authorization
        $this->authorize('accessibleDraft', [Knowledge::class, $knowledge, $service]);

        DB::beginTransaction();

        try {
            $knowledge->fill($request->formAttributes());
            $knowledge->created_rio_id = $rio->id;
            $knowledge->type = Types::ARTICLE;
            $knowledge->is_draft = ArticleTypes::DRAFT;

            $referenceUrls = [];
            $requestData = $request->validated();
            // Get all input urls and convert to json
            foreach ($requestData as $key => $value) {
                $isReferenceUrl = preg_match('/reference/', strval($key));
                if ($isReferenceUrl) {
                    array_push($referenceUrls, $value);
                }
            }

            $knowledge->urls = strval(json_encode($referenceUrls, JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES));

            if ($service->type === ServiceSelectionTypes::RIO) {
                $knowledge->owner_rio_id = $user->rio_id;
            }
            if ($service->type === ServiceSelectionTypes::NEO) {
                $knowledge->owner_neo_id = $service->data->id;
            }

            $knowledge->save();
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
     * Save keyword to session
     *
     * @param string $keyword Search keyword
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function saveSearchToSession($keyword = null)
    {
        try {
            session(['knowledges.search.keyword' => $keyword]);

            return response()->respondSuccess();
        } catch (\Exception $exception) {
            Log::debug($exception);
            report($exception);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Fetch article list based on search keyword
     *
     * @return \Illuminate\View\View
     */
    public function initialSearch()
    {
        // Get keyword from session
        $keyword = session('knowledges.search.keyword') ?? null;
        session()->forget('knowledges.search.keyword');

        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        return view('knowledges.articles.search', compact(
            'rio',
            'service',
            'keyword'
        ));
    }

    /**
     * Fetch articles based on search keyword
     *
     * @param \App\Http\Requests\Knowledge\SearchArticlesRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function search(SearchArticlesRequest $request)
    {
        $requestData = $request->validated();

        $knowledges = Knowledge::getArticlesOnSearch($requestData)->get();

        return ArticleResource::collection($knowledges);
    }

    /**
     * Article detail page
     *
     * @return \Illuminate\View\View
     */
    public function show(Knowledge $knowledge)
    {
        $this->authorize('viewable', [Knowledge::class, $knowledge]);

        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        $commentLists = KnowledgeComment::whereKnowledgeId($knowledge->id)->commentList()->get();

        $comments = CommentResource::collection($commentLists);

        return view('knowledges.articles.show', compact(
            'rio',
            'service',
            'knowledge',
            'comments'
        ));
    }

    /**
     * load Comments
     *
     * @param \App\Models\Knowledge $knowledge
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function loadComments(Knowledge $knowledge, Request $request)
    {
        $page = $request['page'];

        $commentLists = KnowledgeComment::whereKnowledgeId($knowledge->id)->commentList()->paginate(config('bphero.paginate_count'), ['*'], 'page', $page);

        $comments = CommentResource::collection($commentLists);

        return $comments;
    }

    /**
     * PDF Download
     *
     * @param \App\Models\Knowledge $knowledge
     * @return \Illuminate\Http\Response
     */
    public function pdfDownload(Knowledge $knowledge)
    {
        $this->authorize('viewable', [Knowledge::class, $knowledge]);

        try {
            // Generate PDF filename
            $title = str_replace(' ', '_', $knowledge->task_title);
            $filename = urlencode($title) . '.pdf';

            // Generate PDF content
            $pdf = PDF::loadView('knowledges.components.pdf-download', compact('knowledge'));

            // Download PDF
            return $pdf->download($filename);
        } catch (\Exception $exception) {
            Log::debug($exception);
            report($exception);

            return redirect()->back()
                ->withAlertBox('danger', __('Server Error'));
        }
    }
}
