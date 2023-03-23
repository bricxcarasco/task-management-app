<?php

namespace App\Http\Controllers\Classified;

use App\Enums\Classified\SaleProductAccessibility;
use App\Enums\Classified\SaleProductVisibility;
use App\Enums\ServiceSelectionTypes;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Classified\UpdateAccessibilityRequest;
use App\Http\Requests\Classified\ProductCompleteRequest;
use App\Http\Requests\Classified\ProductEditCompleteRequest;
use App\Http\Requests\Classified\ProductEditRequest;
use App\Http\Requests\Classified\ProductRegistrationRequest;
use App\Http\Requests\Classified\SaleProcessUploadRequest;
use App\Http\Requests\FilePond\LoadFileRequest;
use App\Http\Requests\FilePond\ProcessChunkRequest;
use App\Http\Resources\Classified\ClassifiedProductResource;
use App\Http\Resources\Classified\ClassifiedSalesCategoryResource;
use App\Models\ClassifiedContact;
use App\Models\ClassifiedSale;
use App\Models\ClassifiedSaleCategory;
use App\Models\ClassifiedSetting;
use App\Models\User;
use App\Objects\ClassifiedImages;
use App\Objects\FilepondImage;
use App\Objects\ProductEditRegistration;
use App\Objects\ProductRegistration;
use App\Objects\ServiceSelected;
use App\Traits\FilePondUploadable;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Session;
use DB;
use Illuminate\Support\Facades\Storage;

class SaleController extends Controller
{
    use FilePondUploadable;

    /**
     * Controller constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->filepondDisk = config('bphero.public_bucket');
    }

    /**
     * All products list page.
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

        return view('classifieds.sales.index', compact(
            'rio',
            'service',
        ));
    }

    /**
     * Registered Products list page.
     *
     * @return \Illuminate\View\View
     */
    public function registered()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        // Get and check payment setting information
        $cardPayment = ClassifiedSetting::cardPaymentSetting()->first();
        $accountTransfer = ClassifiedSetting::accountTransferSetting()->first();
        $hasPaymentSetting = !empty($cardPayment) || !empty($accountTransfer);

        return view('classifieds.sales.registered-list', compact(
            'rio',
            'service',
            'hasPaymentSetting'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create(Request $request)
    {
        $this->authorize('register', [ClassifiedSale::class, $request]);

        $productCategory = ClassifiedSaleCategory::all();
        $visibility = SaleProductVisibility::asSelectArray();
        $product = new ClassifiedSale();

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        $isProductSession = Session::has('productRegistration');

        if ($isProductSession) {
            //Get product registration session
            $product = ProductRegistration::getSession();
        }

        // Remove product registration session
        $request->session()->remove('productRegistration');

        return view('classifieds.registration.create', compact(
            'productCategory',
            'visibility',
            'service',
            'product',
            'isProductSession'
        ));
    }

    /**
     * View Classified product information.
     *
     * @param \App\Models\ClassifiedSale $product
     * @return \Illuminate\View\View
     */
    public function show(ClassifiedSale $product)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        // Reconstruct product information
        $product = ClassifiedSale::productList()
            ->where('classified_sales.id', $product->id)
            ->firstOrFail();

        // Render 404 page if product is private and
        // not owned by current active service
        if ($product->is_public == false && $product->is_owned == false) {
            abort(404);
        }

        return view('classifieds.sales.show', compact(
            'rio',
            'service',
            'product',
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ClassifiedSale $classifiedSale
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, ClassifiedSale $classifiedSale)
    {
        $this->authorize('register', [ClassifiedSale::class, $classifiedSale]);

        /** @var \App\Models\User */
        $user = auth()->user();

        $productCategory = ClassifiedSaleCategory::all();
        $visibility = SaleProductVisibility::asSelectArray();
        $product = ClassifiedSale::whereId($classifiedSale->id)
            ->first();

        $productId = $classifiedSale->id;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        if ($service->type === ServiceSelectionTypes::RIO && $user->rio->id !== $classifiedSale->selling_rio_id || $service->type === ServiceSelectionTypes::NEO && $service->data->id !== $classifiedSale->selling_neo_id) {
            return redirect()->route('classifieds.sales.index');
        }

        $isProductSession = Session::has('productEditRegistration');

        if ($isProductSession) {
            // Get product registration session
            $product = ProductEditRegistration::getSession()->data ?? $product;
        }

        // Remove product registration session
        $request->session()->remove('productEditRegistration');

        return view('classifieds.registration.edit', compact(
            'productCategory',
            'visibility',
            'service',
            'product',
            'isProductSession',
            'productId'
        ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ClassifiedSale $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ClassifiedSale $product)
    {
        $this->authorize('delete', [ClassifiedSale::class, $product]);
        DB::beginTransaction();

        try {
            $product->delete();

            DB::commit();

            return response()->respondSuccess();
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();

            return response()->respondInternalServerError();
        }
    }

    /**
     * Get products list based on conditions
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function getProducts(Request $request)
    {
        // Filter by conditions
        $conditions = $request->all();

        // Get ALL public products
        $products = ClassifiedSale::productList()
            ->conditions($conditions)
            ->whereIsPublic(SaleProductVisibility::IS_PUBLIC)
            ->paginate(config('bphero.paginate_count'));

        return ClassifiedProductResource::collection($products);
    }

    /**
     * Get registered products list based on conditions
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function getRegisteredProducts(Request $request)
    {
        // Filter by conditions
        $conditions = $request->all();

        // Get service owned/registered products
        $products = ClassifiedSale::sellerList()
            ->conditions($conditions)
            ->paginate(config('bphero.paginate_count'));

        return ClassifiedProductResource::collection($products);
    }

    /**
     * Update accessibility of service owned registered product
     *
     * @param \App\Http\Requests\Classified\UpdateAccessibilityRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAccessibility(UpdateAccessibilityRequest $request, ClassifiedSale $product)
    {
        $this->authorize('update', [ClassifiedSale::class, $product]);

        // Get request data
        $requestData = $request->validated();

        // Update product
        $product->update($requestData);

        return response()->respondSuccess();
    }

    /**
     * Show the form for confirm resource.
     *
     * @param \App\Http\Requests\Classified\ProductRegistrationRequest $request
     * @return mixed
     */
    public function confirmation(ProductRegistrationRequest $request)
    {
        $this->authorize('register', [ClassifiedSale::class, $request]);

        /** @var User */
        $user = auth()->user();
        $product = $request->validated();

        if (!$request->isMethod('post') || Session::has('productRegistration')) {
            return redirect()->route('classifieds.sales.create');
        }

        //Get subject selected session
        $service = ServiceSelected::getSelected();
        //Set product registration session
        ProductRegistration::setSession($product);

        return view('classifieds.registration.confirmation', compact(
            'product',
            'service'
        ));
    }

    /**
     * Show the form for confirm resource.
     *
     * @param \App\Http\Requests\Classified\ProductEditRequest $request
     * @param \App\Models\ClassifiedSale $classifiedSale
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse
     */
    public function editConfirmation(ProductEditRequest $request, ClassifiedSale $classifiedSale)
    {
        $this->authorize('register', [ClassifiedSale::class, $request]);

        /** @var User */
        $user = auth()->user();
        $product = $request->validated();
        $productId = $classifiedSale->id;

        if (!$request->isMethod('post') || Session::has('productEditRegistration')) {
            return redirect()->route('classifieds.sales.edit', $productId);
        }

        //Get subject selected session
        $service = ServiceSelected::getSelected();

        ProductEditRegistration::setSession($product, $classifiedSale);

        return view('classifieds.registration.edit-confirmation', compact(
            'product',
            'service',
            'productId'
        ));
    }

    /**
     * Save product and redirect to product list
     *
     * @param \App\Http\Requests\Classified\ProductCompleteRequest $request
     * @return mixed
     */
    public function complete(ProductCompleteRequest $request)
    {
        $this->authorize('register', [ClassifiedSale::class, $request]);

        /** @var User */
        $user = auth()->user();
        $requestData = $request->validated();

        if (!$request->isMethod('post')) {
            return redirect()->route('classifieds.sales.create');
        }

        // Begin db transaction
        DB::beginTransaction();

        try {
            //Get subject selected session
            $service = ServiceSelected::getSelected();

            // Register classified sale record
            $product = new ClassifiedSale();
            $product->fill($requestData);
            $product->selling_rio_id = $service->type === ServiceSelectionTypes::RIO ? $user->rio->id : null;
            $product->selling_neo_id = $service->type === ServiceSelectionTypes::NEO ? $service->data->id : null;
            $product->created_rio_id = $user->rio->id;
            $product->is_accept = SaleProductAccessibility::OPEN;
            $product->save();

            // Save temporary images
            if (!empty($requestData['upload_file'])) {
                $files = ClassifiedImages::parseFileInputs($requestData['upload_file']);
                ClassifiedImages::saveImages($product, $files);
            }

            DB::commit();
        } catch (\Exception $exception) {
            report($exception);
            DB::rollBack();

            return redirect()
                ->route('classifieds.sales.registered')
                ->withAlertBox('danger', __('Server Error'));
        }

        //Remove product registration session
        $request->session()->remove('productRegistration');

        return redirect()
            ->route('classifieds.sales.registered')
            ->withAlertBox('success', __('listing_is_complete'));
    }

    /**
     * Update product and redirect to product list
     *
     * @param \App\Http\Requests\Classified\ProductEditCompleteRequest $request
     * @param \App\Models\ClassifiedSale $classifiedSale
     * @return mixed
     */
    public function editcomplete(ProductEditCompleteRequest $request, ClassifiedSale $classifiedSale)
    {
        $this->authorize('register', [ClassifiedSale::class, $request]);

        /** @var User */
        $user = auth()->user();
        $requestData = $request->validated();

        if (!$request->isMethod('post')) {
            return redirect()->route('classifieds.sales.edit', $classifiedSale->id);
        }

        DB::beginTransaction();

        try {
            // Update product information
            $classifiedSale->update($requestData);

            // Update contact titles
            $contacts = ClassifiedContact::where('classified_sale_id', $classifiedSale->id);
            if ($contacts->exists()) {
                $contacts->update([
                    'title' => $requestData['title'],
                ]);
            }

            // Save/update images
            $files = ClassifiedImages::parseFileInputs($requestData['upload_file'], $requestData['local_file']);
            ClassifiedImages::saveImages($classifiedSale, $files);

            // Remove product registration session
            $request->session()->remove('productEditRegistration');

            DB::commit();

            return redirect()
                ->route('classifieds.sales.registered')
                ->withAlertBox('success', __('listing_is_complete'));
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();

            return redirect()
                ->route('classifieds.sales.registered')
                ->withAlertBox('danger', __('Server Error'));
        }
    }

    /**
     * Process Upload API Endpoint
     *
     * @param \App\Http\Requests\Classified\SaleProcessUploadRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function processUpload(SaleProcessUploadRequest $request)
    {
        // Get request data
        $requestData = $request->validated();
        $files = $requestData['upload_file'];

        // Check if data sent is a file
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $result = FilepondImage::storeTemporaryFile($file, $this->filepondDisk, true);

                // Return failed save
                if ($result === false) {
                    return response()->respondInternalServerError(['Could not save file.']);
                }

                // Return server ID
                return response($result, 200, [
                    'Content-Type' => 'text/plain',
                ]);
            }

            // Handle Filepond File Metadata (usually on chunk upload)
            if (CommonHelper::isJson($file)) {
                $path = FilepondImage::generateTemporaryDirectory();
                $serverCode = FilepondImage::toServerCode($path);

                // Return server ID
                return response($serverCode, 200, [
                    'Content-Type' => 'text/plain',
                ]);
            }
        }

        // Return failed save
        return response()->respondInternalServerError(['Could not save file.']);
    }

    /**
     * Process chunk uploaded file
     *
     * @param \App\Http\Requests\FilePond\ProcessChunkRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function processChunk(ProcessChunkRequest $request)
    {
        try {
            $requestData = $request->validated();

            // Initialize filepond file
            $filepond = new FilepondImage($requestData['patch'], true, $this->filepondDisk);

            // Save patch data to a temporary file
            if (!$filepond->exists($requestData['filename'])) {
                $filepond->createTemporaryPatchFile($requestData['offset']);

                // Guard clause for unfinished patch upload
                if (!$filepond->isPatchUploadComplete($requestData['length'])) {
                    return response()->respondNoContent();
                }

                // Compile patch files to a single file
                $filepond->compilePatchFiles($requestData['filename'], true);
            }

            return response()->respondNoContent();
        } catch (\Exception $exception) {
            return response()->respondInternalServerError([$exception->getMessage()]);
        }
    }

    /**
     * Load already uploaded file to filepond
     *
     * @param \App\Http\Requests\FilePond\LoadFileRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function loadFile(LoadFileRequest $request)
    {
        $requestData = $request->validated();
        $storage = Storage::disk($this->filepondDisk);

        // Decode url
        $decodedPath = rawurldecode($requestData['path']);
        $targetPath = CommonHelper::removeMainDirectoryPath($decodedPath);

        // Check if file is existing
        if (!$storage->exists($targetPath)) {
            return response()->respondNotFound();
        }

        // Prepare information
        $file = $storage->get($targetPath);
        $mimeType = $storage->mimeType($targetPath);
        $fileName = CommonHelper::getBasename($targetPath);

        // Prepare filename
        $targetFilename = ClassifiedImages::removePrefix($fileName);
        if (!empty($targetFilename)) {
            $fileName = $targetFilename;
        }

        return response($file)
            ->withHeaders([
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
    }

    /**
     * Get sales categories list
     *
     * @return mixed
     */
    public function getSalesCategories()
    {
        // Get service owned/registered products
        $productCategories = ClassifiedSaleCategory::all();

        return ClassifiedSalesCategoryResource::collection($productCategories);
    }
}
