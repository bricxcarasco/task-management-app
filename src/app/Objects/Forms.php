<?php

namespace App\Objects;

use App\Enums\Form\ProductTaxDistinction;
use App\Enums\Form\Types;
use Carbon\Carbon;

class Forms
{
    /**
     * Generate PDF file name
     *
     * @param \App\Models\Form $form
     * @return string
     */
    public static function generatePdfFilename($form)
    {
        $now = Carbon::now();
        $formatedDate = $now->format('Ymd');

        switch ($form->type) {
            case Types::INVOICE:
                /** @var string */
                $type = __('Invoice');
                break;
            case Types::PURCHASE_ORDER:
                /** @var string */
                $type = __('Purchase order');
                break;
            case Types::DELIVERY_SLIP:
                /** @var string */
                $type = __('Delivery slip');
                break;
            case Types::RECEIPT:
                /** @var string */
                $type = __('Receipt');
                break;
            default:
                /** @var string */
                $type = __('Quotation2');
                break;
        }

        // Replace spaces with underscore
        $title = str_replace(' ', '_', $form->title);

        // URL encode texts
        $encodedType = urlencode($type);
        $encodedTitle = urlencode($title);

        // Construct filename
        $fileName = "{$encodedType}_{$encodedTitle}_{$formatedDate}.pdf";

        return $fileName;
    }

    /**
     * Generate PDF file name for Form Document Linkage
     *
     * @param \App\Models\Form $form
     * @return string
     */
    public static function generateDocumentLinkagePdfFilename($form)
    {
        $now = Carbon::now();
        $formatedDate = $now->format('Ymd');

        switch ($form->type) {
            case Types::INVOICE:
                $type = __('Invoice');
                break;
            case Types::PURCHASE_ORDER:
                $type = __('Purchase order');
                break;
            case Types::DELIVERY_SLIP:
                $type = __('Delivery slip');
                break;
            case Types::RECEIPT:
                $type = __('Receipt');
                break;
            default:
                $type = __('Quotation2');
                break;
        }

        /** @var string */
        $formType = $type;

        // Replace spaces with underscore
        $title = str_replace(' ', '_', $form->title);

        $fileName = $formType . '_' . $title . '_' . $formatedDate . '.pdf';

        return $fileName;
    }

    /**
     * Generate CSV filename.
     *
     * @param int $type
     * @return string
     */
    public static function generateCsvFilename($type)
    {
        $now = Carbon::now()->format('Ymd');

        switch ($type) {
            case Types::INVOICE:
                /** @var string */
                $formTypeText = __('Invoice');
                break;
            case Types::PURCHASE_ORDER:
                /** @var string */
                $formTypeText = __('Purchase order');
                break;
            case Types::DELIVERY_SLIP:
                /** @var string */
                $formTypeText = __('Delivery slip');
                break;
            case Types::RECEIPT:
                /** @var string */
                $formTypeText = __('Receipt');
                break;
            default:
                /** @var string */
                $formTypeText = __('Quotation2');
                break;
        }

        return $formTypeText . '_' . $now . '.csv';
    }

    /**
     * Define CSV columns
     *
     * @return array
     */
    public static function getCsvColumns()
    {
        return [
            __('Supplier'),
            __('Form No'),
            __('Subject'),
            __('Price without tax'),
            __('Total tax'),
            __('Price'),
            __('Postal code'),
            __('Address2'),
            __('Delivery location'),
            __('Payment terms'),
            __('Delivery deadline'),
            __('Delivery Date'),
            __('Payment date'),
            __('Date of issue'),
            __('Term of validity'),
            __('Receipt date 2'),
            __('Remarks'),
            __('Issuer (Store name / trade name)'),
            __('Issuer (Department name)'),
            __('Issuer (Address)'),
            __('Issuer (TEL)'),
            __('Issuer (FAX)'),
            __('Issuer (Business number)'),
            __('Payee Information'),
            __('Payment Notes'),
            __('Refer Receipt Number'),
            __('Product name 2'),
            __('Quantity'),
            __('Unit'),
            __('Unit price'),
            __('Tax Distinction'),
            __('Sub Total'),
            __('Tax amount'),
            __('Price with tax'),
        ];
    }

    /**
     * Set a CSV row based on form data
     *
     * @param object $data
     * @param bool $isReceipt
     * @return array
     */
    public static function setCsvRow($data, $isReceipt = false)
    {
        if ($isReceipt) {
            return self::setReceiptCsvRow($data);
        }

        return self::setFormCsvRow($data);
    }

    /**
     * Set a CSV row for Receipt form
     *
     * @param object $data
     * @return array
     */
    public static function setReceiptCsvRow($data)
    {
        // Format dates
        $issueDate = !empty($data->issue_date)
            ? Carbon::parse($data->issue_date)->format('Y/m/d') : null;
        $receiptDate = !empty($data->receipt_date)
            ? Carbon::parse($data->receipt_date)->format('Y/m/d') : null;

        return [
            __('Supplier') => $data->name ?? null, /* @phpstan-ignore-line */
            __('Form No') => $data->form_no ?? null, /* @phpstan-ignore-line */
            __('Subject') => $data->title ?? null, /* @phpstan-ignore-line */
            __('Price without tax') => null, /* @phpstan-ignore-line */
            __('Total tax') => null, /* @phpstan-ignore-line */
            __('Price') => $data->price ?? 0, /* @phpstan-ignore-line */
            __('Postal code') => $data->zipcode ?? null, /* @phpstan-ignore-line */
            __('Address2') => $data->address ?? null, /* @phpstan-ignore-line */
            __('Delivery location') => $data->delivery_address ?? null, /* @phpstan-ignore-line */
            __('Payment terms') => $data->payment_terms ?? null, /* @phpstan-ignore-line */
            __('Delivery deadline') => null, /* @phpstan-ignore-line */
            __('Delivery Date') => null, /* @phpstan-ignore-line */
            __('Payment date') => null, /* @phpstan-ignore-line */
            __('Date of issue') => $issueDate ?? null, /* @phpstan-ignore-line */
            __('Term of validity') => null, /* @phpstan-ignore-line */
            __('Receipt date 2') => $receiptDate ?? null, /* @phpstan-ignore-line */
            __('Remarks') => $data->remarks ?? null, /* @phpstan-ignore-line */
            __('Issuer (Store name / trade name)') => $data->issuer_name ?? null, /* @phpstan-ignore-line */
            __('Issuer (Department name)') => $data->issuer_department_name ?? null, /* @phpstan-ignore-line */
            __('Issuer (Address)') => $data->issuer_address ?? null, /* @phpstan-ignore-line */
            __('Issuer (TEL)') => $data->issuer_tel ?? null, /* @phpstan-ignore-line */
            __('Issuer (FAX)') => $data->issuer_fax ?? null, /* @phpstan-ignore-line */
            __('Issuer (Business number)') => $data->issuer_business_number ?? null, /* @phpstan-ignore-line */
            __('Payee Information') => $data->issuer_payee_information ?? null, /* @phpstan-ignore-line */
            __('Payment Notes') => $data->issuer_payment_notes ?? null, /* @phpstan-ignore-line */
            __('Refer Receipt Number') => $data->refer_receipt_no ?? null, /* @phpstan-ignore-line */
            __('Product name 2') => null, /* @phpstan-ignore-line */
            __('Quantity') => null, /* @phpstan-ignore-line */
            __('Unit') => null, /* @phpstan-ignore-line */
            __('Unit price') => null, /* @phpstan-ignore-line */
            __('Tax Distinction') => null, /* @phpstan-ignore-line */
            __('Sub Total') => null, /* @phpstan-ignore-line */
            __('Tax amount') => null, /* @phpstan-ignore-line */
            /* @phpstan-ignore-next-line */
            __('Price with tax') => null, /* @phpstan-ignore-line */
        ];
    }

    /**
     * Set a CSV row for any form type except Receipt
     *
     * @param object $data
     * @return array
     */
    public static function setFormCsvRow($data)
    {
        $form = $data->form;
        $pricesAndTaxes = FormProductCalculations::getPricesAndTaxes($form->products);

        // Format dates
        $deliveryDate = !empty($form->delivery_date)
            ? Carbon::parse($form->delivery_date)->format('Y/m/d') : null;
        $deliveryDeadline = !empty($form->delivery_deadline)
            ? Carbon::parse($form->delivery_deadline)->format('Y/m/d') : null;
        $paymentDate = !empty($form->payment_date)
            ? Carbon::parse($form->payment_date)->format('Y/m/d') : null;
        $issueDate = !empty($form->issue_date)
            ? Carbon::parse($form->issue_date)->format('Y/m/d') : null;
        $expirationDate = !empty($form->expiration_date)
            ? Carbon::parse($form->expiration_date)->format('Y/m/d') : null;
        $receiptDate = !empty($form->receipt_date)
            ? Carbon::parse($form->receipt_date)->format('Y/m/d') : null;

        return [
            __('Supplier') => $data->supplier_name ?? null, /* @phpstan-ignore-line */
            __('Form No') => $form->form_no ?? null, /* @phpstan-ignore-line */
            __('Subject') => $form->title ?? null, /* @phpstan-ignore-line */
            __('Price without tax') => $pricesAndTaxes->total_price ?? 0, /* @phpstan-ignore-line */
            __('Total tax') => $pricesAndTaxes->total_tax ?? 0, /* @phpstan-ignore-line */
            __('Price') => $pricesAndTaxes->overall_total ?? 0, /* @phpstan-ignore-line */
            __('Postal code') => $form->zipcode ?? null, /* @phpstan-ignore-line */
            __('Address2') => $form->address ?? null, /* @phpstan-ignore-line */
            __('Delivery location') => $form->delivery_address ?? null, /* @phpstan-ignore-line */
            __('Payment terms') => $form->payment_terms ?? null, /* @phpstan-ignore-line */
            __('Delivery deadline') => $deliveryDate ?? null, /* @phpstan-ignore-line */
            __('Delivery Date') => $deliveryDeadline ?? null, /* @phpstan-ignore-line */
            __('Payment date') => $paymentDate ?? null, /* @phpstan-ignore-line */
            __('Date of issue') => $issueDate ?? null, /* @phpstan-ignore-line */
            __('Term of validity') => $expirationDate ?? null, /* @phpstan-ignore-line */
            __('Receipt date 2') => $receiptDate ?? null, /* @phpstan-ignore-line */
            __('Remarks') => $form->remarks ?? null, /* @phpstan-ignore-line */
            __('Issuer (Store name / trade name)') => $form->issuer_name ?? null, /* @phpstan-ignore-line */
            __('Issuer (Department name)') => $form->issuer_department_name ?? null, /* @phpstan-ignore-line */
            __('Issuer (Address)') => $form->issuer_address ?? null, /* @phpstan-ignore-line */
            __('Issuer (TEL)') => $form->issuer_tel ?? null, /* @phpstan-ignore-line */
            __('Issuer (FAX)') => $form->issuer_fax ?? null, /* @phpstan-ignore-line */
            __('Issuer (Business number)') => $form->issuer_business_number ?? null, /* @phpstan-ignore-line */
            __('Payee Information') => $form->issuer_payee_information ?? null, /* @phpstan-ignore-line */
            __('Payment Notes') => $form->issuer_payment_notes ?? null, /* @phpstan-ignore-line */
            __('Refer Receipt Number') => $form->refer_receipt_no ?? null, /* @phpstan-ignore-line */
            __('Product name 2') => $data->name ?? null, /* @phpstan-ignore-line */
            __('Quantity') => $data->quantity ?? null, /* @phpstan-ignore-line */
            __('Unit') => $data->unit ?? null, /* @phpstan-ignore-line */
            __('Unit price') => $data->unit_price ?? null, /* @phpstan-ignore-line */
            __('Tax Distinction') => ProductTaxDistinction::getDescription($data->tax_distinction) ?? null, /* @phpstan-ignore-line */
            __('Sub Total') => FormProductCalculations::calculateTotalAmount($data), /* @phpstan-ignore-line */
            __('Tax amount') => FormProductCalculations::calculateTaxAmount($data), /* @phpstan-ignore-line */
            /* @phpstan-ignore-next-line */
            __('Price with tax') => FormProductCalculations::calculateProductTotal($data), /* @phpstan-ignore-line */
        ];
    }
}
