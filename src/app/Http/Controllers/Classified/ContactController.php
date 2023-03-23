<?php

namespace App\Http\Controllers\Classified;

use App\Enums\Classified\MessageSender;
use App\Enums\ServiceSelectionTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Classified\SendInquiryRequest;
use App\Http\Resources\Classified\ClassifiedContactResource;
use App\Models\ClassifiedContact;
use App\Models\ClassifiedContactMessage;
use App\Models\ClassifiedSale;
use App\Models\Neo;
use App\Models\Rio;
use App\Objects\ServiceSelected;
use DB;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
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

        return view('classifieds.contacts.index', compact(
            'rio',
            'service',
        ));
    }

    /**
     * Send inquiry from product buyer to seller.
     *
     * @param \App\Http\Requests\Classified\SendInquiryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendInquiry(SendInquiryRequest $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get request data
        $requestData = $request->validated();

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        // Fetch product information
        /** @var \App\Models\ClassifiedSale */
        $product = ClassifiedSale::whereId($requestData['classified_sale_id'])->first();

        DB::beginTransaction();

        try {
            // Create buyer contact data
            $buyerContact = ClassifiedContact::create([
                'classified_sale_id' => $product->id,
                'rio_id' => $service->type === ServiceSelectionTypes::RIO ? $service->data->id : null,
                'neo_id' => $service->type === ServiceSelectionTypes::NEO ? $service->data->id : null,
                'selling_rio_id' => $product->selling_rio_id ?? null,
                'selling_neo_id' => $product->selling_neo_id ?? null,
                'title' => $product->title,
                'created_rio_id' => $user->rio_id,
            ]);

            // Create inquiry message
            $classifiedContactMessage = ClassifiedContactMessage::create([
                'classified_contact_id' => $buyerContact->id,
                'sender' => MessageSender::BUYER,
                'message' => $requestData['message'],
                'attaches' => null,
            ]);

            DB::commit();

            //Get sender based on service type
            $sender = null;
            $receiver = null;

            if ($service->type === ServiceSelectionTypes::RIO) {
                $sender =  Rio::whereId($service->data->id)->first();
            }

            if ($service->type === ServiceSelectionTypes::NEO) {
                $sender =  Neo::whereId($service->data->id)->first();
            }

            $receiver = Rio::whereId($buyerContact->selling_rio_id)->first()
                ?? Neo::whereId($buyerContact->selling_neo_id)->first();

            $classifiedContactMessage->sendNetshopChatEmail($sender, $receiver, $classifiedContactMessage);

            return response()->respondSuccess();
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();

            return response()->respondInternalServerError();
        }
    }

    /**
     * Get inquiries list based on conditions
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getInquiries(Request $request)
    {
        // Filter by conditions
        $conditions = $request->all();

        // Get inquiries
        $inquiries = ClassifiedContact::inquiryList()
            ->commonConditions($conditions)
            ->paginate(config('bphero.paginate_count'));

        return ClassifiedContactResource::collection($inquiries);
    }
}
