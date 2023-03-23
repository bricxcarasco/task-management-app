<?php

namespace App\Http\Controllers\Form;

use App\Enums\Form\Types;
use App\Http\Controllers\Controller;
use App\Http\Resources\Form\FormResource;
use App\Http\Requests\Form\QuotationSearchInputRequest;
use App\Models\Form;
use App\Models\FormBasicSetting;
use App\Objects\FormProductCalculations;
use App\Objects\ServiceSelected;
use DB;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    /**
     * Create quotation view
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

        return view('forms.quotations.index', compact(
            'rio',
            'service',
        ));
    }

    /**
     * Quotation list page
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        $formBasicSetting = FormBasicSetting::serviceSetting($service)->first();

        return view('forms.quotations.create', compact(
            'rio',
            'service',
            'formBasicSetting'
        ));
    }

    /**
     * Form quotation create duplicate.
     *
     * @param \App\Models\Form $form
     * @return \Illuminate\Contracts\View\View|void
     */
    public function duplicate(Form $form)
    {
        $this->authorize('duplicate', [Form::class, $form, Types::QUOTATION]);

        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        $formBasicSetting = FormBasicSetting::currentEntityServiceSetting()->first();

        // Unset properties that will not be used
        unset(
            $form->form_no,
            $form->delivery_date,
            $form->delivery_deadline,
            $form->issue_date,
            $form->payment_date,
            $form->expiration_date,
            $form->receipt_date,
        );

        return view('forms.quotations.duplicate', compact(
            'rio',
            'service',
            'formBasicSetting',
            'form'
        ));
    }

    /**
     * Get quotation lists
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function getQuotationLists(Request $request)
    {
        $requestData = $request->all();

        $quotation = Form::formList(Types::QUOTATION)
            ->commonConditions($requestData)
            ->paginate(config('bphero.paginate_count'));

        return FormResource::collection($quotation);
    }

    /**
     * Validate quotation search
     *
     * @param \App\Http\Requests\Form\QuotationSearchInputRequest $request
     * @return mixed
     */
    public function validateQuotationSearch(QuotationSearchInputRequest $request)
    {
        $requestData = $request->validated();

        return response()->respondSuccess($requestData);
    }

    /**
     * Display Quotation details.
     *
     * @param \App\Models\Form $form
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function show(Form $form)
    {
        /** @var \App\Models\User */
        $user = auth()->user();
        $rio = $user->rio;

        // Get selected service
        $service = ServiceSelected::getSelected();

        // Get ALL necessary form information
        $form = $form
            ->formDetails(Types::QUOTATION)
            ->where('forms.id', $form->id)
            ->firstOrFail();

        // Calculate product prices and taxes
        $pricesAndTaxes = FormProductCalculations::getPricesAndTaxes($form->products);

        return view('forms.quotations.show', compact(
            'form',
            'service',
            'pricesAndTaxes'
        ));
    }

    /**
     * Form quotation edit page.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Form $form
     * @return \Illuminate\Contracts\View\View|void
     */
    public function editQuotation(Request $request, Form $form)
    {
        $this->authorize('edit', [Form::class, $form]);

        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        $formBasicSetting = FormBasicSetting::serviceSetting($service)->first();

        if (empty($formBasicSetting)) {
            $formBasicSetting = FormBasicSetting::getSettingFromRioNeo($service);
        }

        return view('forms.quotations.edit', compact(
            'rio',
            'service',
            'form',
            'formBasicSetting'
        ));
    }

    /**
     * Form quotation deletion.
     *
     * @param \App\Models\Form $form
     * @param bool $withAlert. Defaults to false.
     * @return mixed
     */
    public function destroy(Form $form, $withAlert = false)
    {
        $this->authorize('delete', [Form::class, $form]);

        /** @var \App\Models\User */
        $user = auth()->user();

        DB::beginTransaction();

        try {
            // Update deleter RIO and delete the form
            $form->update([
                'deleter_rio_id' => $user->rio_id,
            ]);
            $form->delete();

            DB::commit();

            if ($withAlert) {
                session()->put('alert', [
                    'status' => 'success',
                    'message' => __('Form has been deleted'),
                ]);
            }

            return response()->respondSuccess(
                route('forms.quotations.index')
            );
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);

            if ($withAlert) {
                session()->put('alert', [
                    'status' => 'danger',
                    'message' => __('Server Error'),
                ]);
            }

            return response()->respondInternalServerError();
        }
    }

    /**
     * Quotation CSV download page
     *
     * @return \Illuminate\View\View
     */
    public function csvDownloadList()
    {
        // Set form type
        $type = Types::QUOTATION;

        return view('forms.csv-download', compact(
            'type',
        ));
    }
}
