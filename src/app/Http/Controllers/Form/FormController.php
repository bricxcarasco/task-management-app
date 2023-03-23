<?php

namespace App\Http\Controllers\Form;

use App\Enums\Form\Types;
use App\Http\Controllers\Controller;
use App\Http\Resources\Form\DeleteHistoryResource;
use App\Http\Resources\Form\UpdateHistoryResource;
use App\Exceptions\ServiceSessionNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Models\Form;
use App\Models\FormHistory;
use App\Models\User;
use App\Models\Document;
use App\Enums\ServiceSelectionTypes;
use App\Enums\Document\DocumentTypes;
use App\Enums\Document\StorageTypes;
use App\Models\FormProduct;
use App\Objects\Forms;
use App\Objects\ServiceSelected;
use App\Objects\FormProductCalculations;
use App\Http\Requests\Form\FormHistoryRequest;
use App\Services\CsvService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Session;
use DB;

class FormController extends Controller
{
    /**
     * delete Histroy page
     *
     * @param string $formType
     * @return mixed
     */
    public function deleteHistory(string $formType)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        return view('forms.delete-history', compact(
            'rio',
            'service',
            'formType',
        ));
    }

    /**
     * Get single form history
     *
     * @param FormHistoryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function getFormHistory(FormHistoryRequest $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        $requestData = $request->validated();

        $deletedForm = Form::withTrashed()
            ->with('deleted_products')
            ->where('forms.id', $requestData['id'])
            ->where('forms.type', $requestData['form_type'])
            ->where('forms.deleter_rio_id', $user->rio->id)
            ->whereNotNull('forms.deleted_at')
            ->first();

        return response()->respondSuccess($deletedForm);
    }

    /**
     * Update Histroy page
     *
     * @return mixed
     */
    public function updateHistory(Form $form)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();
        $formType = Types::fromValue($form->type);
        $form = $form
            ->formDetails($form->type)
            ->where('forms.id', $form->id)
            ->first();

        //check if user is owner
        if (!$form) {
            return redirect()->back();
        }

        return view('forms.update-history', compact(
            'rio',
            'service',
            'form',
            'formType',
        ));
    }

    /**
     * Get update history base on current form
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function getUpdateHistoryLists(Request $request)
    {
        $requestData = $request->all();

        $updateHistory = FormHistory::whereFormId($requestData['form_id'])
            ->orderBy('created_at', 'DESC')
            ->paginate(config('bphero.paginate_count'));

        return UpdateHistoryResource::collection($updateHistory);
    }

    /**
     * Get update history details
     *
     * @param integer $id
     * @return mixed
     */
    public function getUpdateHistoryDetails($id)
    {
        $form_history = FormHistory::with('products')->findOrFail($id);
        $pricesAndTaxes = FormProductCalculations::getPricesAndTaxes($form_history->products);

        return response()->json([
            'formHistory' =>  $form_history,
            'pricesAndTaxes' => $pricesAndTaxes
        ]);
    }

    /**
     * Get delete history base on current form type
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function getDeleteHistoryLists(Request $request)
    {
        $requestData = $request->all();

        $deleteHistory = Form::formList($requestData['form_type'])
            ->deletedForms()
            ->paginate(config('bphero.paginate_count'));

        return DeleteHistoryResource::collection($deleteHistory);
    }

    /**
     * PDF Download
     *
     * @return \Illuminate\Http\Response
     */
    public function pdfDownload(form $form)
    {
        // Get ALL necessary form information
        $form = $form
            ->formDetails($form->type)
            ->where('forms.id', $form->id)
            ->firstOrFail();

        if ($form->type === Types::RECEIPT) {
            // Generate PDF for Receipt
            $pdf = PDF::loadView('forms.components.pdf-download-receipt', compact('form'));
        } else {
            // Get prices and taxes
            $pricesAndTaxes = FormProductCalculations::getPricesAndTaxes($form->products);

            // Default image path
            $imagePath = null;

            // S3 related paths
            $isStorage = strpos($form->issuer_detail_logo, '/storage');
            $targetDirectory = config('bphero.form_issuer_image');
            $storagePath = "{$targetDirectory}/{$form->issuer_image}";
            $disk = Storage::disk(config('bphero.public_bucket'));

            if ($isStorage) {
                $image = explode('/storage', $form->issuer_detail_logo)[1];

                if (file_exists(public_path('storage' . $image))) {
                    $imagePath = public_path('storage' . $image);
                }
            } else {
                if (isset($form->issuer_detail_logo) && $disk->exists($storagePath)) {
                    $imagePath = "data:image/png;base64, {$form->issuer_detail_logo_raw}";
                }
            }

            $pdf = PDF::loadView('forms.components.pdf-download', compact(
                'form',
                'isStorage',
                'imagePath',
                'pricesAndTaxes'
            ));
        }
        // Generate PDF filename
        $fileName = Forms::generatePdfFilename($form);

        // Download PDF
        return $pdf->download($fileName);
    }

    /**
     * Upload PDF to S3 Folder
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadFormPdfFormat(Request $request)
    {
        /** @var User */
        $user = auth()->user();

        try {

            /** @var Form */
            $form = Form::findOrFail($request->form_id);

            $service = ServiceSelected::getSelected();

            // Generate target directory
            switch ($service->type) {
                case ServiceSelectionTypes::RIO:
                    $targetDirectory = config('bphero.rio_document_storage_path') . $service->data->id;
                    break;
                case ServiceSelectionTypes::NEO:
                    $targetDirectory = config('bphero.neo_document_storage_path') . $service->data->id;
                    break;
                default:
                    $targetDirectory = null;
                    break;
            }

            // Handle invalid service type session
            if (empty($targetDirectory)) {
                throw new ServiceSessionNotFoundException();
            }

            // Get ALL necessary form information
            $form = $form
                ->formDetails($form->type)
                ->where('forms.id', $form->id)
                ->firstOrFail();
            $pricesAndTaxes = FormProductCalculations::getPricesAndTaxes($form->products);

            $fileName = Forms::generateDocumentLinkagePdfFilename($form);

            //Image path
            if ($form->type === Types::RECEIPT) {
                // Generate PDF for Receipt
                $pdf = PDF::loadView('forms.components.pdf-download-receipt', compact('form'));
            } else {
                // Get prices and taxes
                $pricesAndTaxes = FormProductCalculations::getPricesAndTaxes($form->products);

                // Default image path
                $imagePath = asset('images/profile/user_default.png');

                // S3 related paths
                $isStorage = strpos($form->issuer_detail_logo, '/storage');
                $targetDirectory = config('bphero.form_issuer_image');
                $storagePath = "{$targetDirectory}/{$form->issuer_image}";
                $disk = Storage::disk(config('bphero.public_bucket'));

                if ($isStorage) {
                    $image = explode('/storage', $form->issuer_detail_logo)[1];

                    if (file_exists(public_path('storage' . $image))) {
                        $imagePath = public_path('storage' . $image);
                    }
                } else {
                    if (isset($form->issuer_detail_logo) && $disk->exists($storagePath)) {
                        $imagePath = "data:image/png;base64, {$form->issuer_detail_logo_raw}";
                    }
                }

                $pdf = PDF::loadView('forms.components.pdf-download', compact(
                    'form',
                    'isStorage',
                    'imagePath',
                    'pricesAndTaxes'
                ));
            }

            // Upload generated form pdf file to S3 folder
            $targetPath = $targetDirectory . '/' . $fileName;
            Storage::disk(config('bphero.private_bucket'))->put($targetPath, $pdf->output());

            // Create file data in database
            $documentFile = new Document();

            if ($service->type === ServiceSelectionTypes::RIO) {
                $documentFile->owner_rio_id = $service->data->id;
            }

            if ($service->type === ServiceSelectionTypes::NEO) {
                $documentFile->owner_neo_id = $service->data->id;
            }

            $documentFile->directory_id = $request['directory_id'];
            $documentFile->document_type = DocumentTypes::FILE;
            $documentFile->document_name = $fileName;
            $documentFile->mime_type = 'application/pdf';
            $documentFile->storage_type_id = StorageTypes::HERO;
            $documentFile->storage_path = config('bphero.private_directory') . '/' . $targetPath;
            $documentFile->file_bytes = Storage::disk(config('bphero.private_bucket'))->size($targetPath);
            $documentFile->save();

            return response()->respondSuccess();
        } catch (NotFoundHttpException $e) {
            return response()->respondNotFound();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return response()->respondInternalServerError($e);
        }
    }

    /**
     * Export CSV files as Zipfile
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Services\CsvService $csvService
     * @return mixed
     */
    public function exportCsv(Request $request, CsvService $csvService)
    {
        // Get request data
        $requestData = $request->all();

        if (!$request->has('form_ids')) {
            return redirect()->back()
                ->withAlertBox('danger', __('No data to download'));
        }

        try {
            // Define rows and columns
            $columns = Forms::getCsvColumns();
            $rows = [];

            if ((int)$requestData['type'] !== Types::RECEIPT) {
                $downloadableCsvs = FormProduct::downloadableCsv(
                    $requestData['form_ids'],
                    $requestData['type'],
                )->get();

                foreach ($downloadableCsvs as $data) {
                    $rows[] = Forms::setCsvRow($data);
                }
            } else {
                $downloadableCsvs = Form::formList($requestData['type'])
                    ->whereIn('forms.id', $requestData['form_ids'])
                    ->get();

                foreach ($downloadableCsvs as $data) {
                    $rows[] = Forms::setCsvRow($data, true);
                }
            }

            if (empty($downloadableCsvs)) {
                return redirect()->back()
                    ->withAlertBox('danger', __('No data to download'));
            }

            // Generate CSV filename
            $fileName = Forms::generateCsvFilename($requestData['type']);

            // Export CSV file
            return $csvService->export($columns, $rows, $fileName);
        } catch (\Exception $e) {
            report($e);

            return redirect()->back()
                ->withAlertBox('danger', __('Failed to download CSV'));
        }
    }
}
