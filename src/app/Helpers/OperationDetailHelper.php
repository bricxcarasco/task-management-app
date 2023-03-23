<?php

namespace App\Helpers;

use App\Enums\Form\Types;

/**
 * App\Helpers\OperationDetailHelper
 */
class OperationDetailHelper
{
    /**
     * Generate operation message for form registration.
     *
     * @param string $title Newly registered form title
     * @return string
     */
    public static function getRegistrationMessage($title)
    {
        return self::registration($title);
    }

    /**
     * Generate operation message for form edit based on form type.
     *
     * @param \App\Models\Form $existingForm Existing form
     * @param array $requestData Request data
     * @param int $type Form type. Defaults to 1.
     * @return string|null
     */
    public static function getEditMessage($existingForm, $requestData, $type = 1)
    {
        // Initialize variables
        $updatedForm = [];
        $updatedProducts = [];
        $formFields = [];
        $message = null;

        switch ($type) {
            case Types::QUOTATION:
                [$updatedForm, $updatedProducts] = self::compareQuotations($existingForm, $requestData);
                $formFields = self::quotationFormFields();
                break;
            case Types::PURCHASE_ORDER:
                [$updatedForm, $updatedProducts] = self::comparePurchaseOrders($existingForm, $requestData);
                $formFields = self::purchaseOrderFormFields();
                break;
            case Types::DELIVERY_SLIP:
                [$updatedForm, $updatedProducts] = self::compareDeliverySlips($existingForm, $requestData);
                $formFields = self::deliverySlipFormFields();
                break;
            case Types::INVOICE:
                [$updatedForm, $updatedProducts] = self::compareInvoices($existingForm, $requestData);
                $formFields = self::invoiceFormFields();
                break;
            case Types::RECEIPT:
                [$updatedForm] = self::compareReceipts($existingForm, $requestData);
                $formFields = self::receiptFormFields();
                break;
            default:
                return null;
        }

        if (empty($updatedForm) && empty($updatedProducts)) {
            return null;
        }

        // Set basic information messages
        $message .= self::compileBasicInfoMessages($updatedForm, $formFields);

        if ($type !== Types::RECEIPT) {
            // Set product messages
            $message .= self::compileProductMessages($updatedProducts);
        }

        return $message;
    }

    /**
     * Compile basic information messages into one string.
     *
     * @param array $updatedForm Updated form fields
     * @param array $formFields Form field labels
     * @return string|null
     */
    private static function compileBasicInfoMessages($updatedForm, $formFields)
    {
        $message = null;

        foreach ($updatedForm as $field) {
            $formField = $formFields[$field];
            $message .= self::editBasicInformation($formField) . "\r\n";
        }

        return $message;
    }

    /**
     * Compile product information messages into one string.
     *
     * @param array $products Changes made to form products
     * @return string|null
     */
    private static function compileProductMessages($products)
    {
        $addedProducts = $products['added'] ?? [];
        $deletedProducts = $products['deleted'] ?? [];
        $message = null;

        // Added products
        foreach ($addedProducts as $productName) {
            $message .= self::addProduct($productName) . "\r\n";
        }

        // Deleted products
        foreach ($deletedProducts as $productName) {
            $message .= self::deleteProduct($productName) . "\r\n";
        }

        return $message;
    }

    /**
     * Operation details - Registration
     *
     * When issuing a form:
     * "{forms.title} を発行しました。 "
     *
     * @param string $title New form title
     * @return string
     */
    public static function registration($title)
    {
        /** @var string */
        return __('Operation - Registration', ['title' => $title]);
    }

    /**
     * Operation details - Basic Information edit
     *
     * When updating the items of basic information, issuer information,
     * remarks (including payment notes and transfer destination
     * information in the case of invoices)
     *
     * "基本情報 > {Item name}を更新しました。"
     *
     * Example：When editing the "Title" part in "Basic Information"
     * "基本情報 > 件名を更新しました。"
     *
     * @param string $field Updated basic info field
     * @return string
     */
    public static function editBasicInformation($field)
    {
        /** @var string */
        return __('Operation - Edit Basic Information', ['field' => $field]);
    }

    /**
     * Operation details - Add Product
     *
     * When adding a specific item
     * "品目 > {form_products.name}を追加しました。"
     *
     * @param string $name Product name
     * @return string
     */
    public static function addProduct($name)
    {
        /** @var string */
        return __('Operation - Add Product', ['name' => $name]);
    }

    /**
     * Operation details - Delete Product
     *
     * When a specific item is deleted
     * "品目 > {form_products.name}を削除しました。"
     *
     * @param string $name Product name
     * @return string
     */
    public static function deleteProduct($name)
    {
        /** @var string */
        return __('Operation - Delete Product', ['name' => $name]);
    }

    /**
     * Get issuer image
     *
     * @param \App\Models\Form $form Existing form
     * @param array $data Request data
     * @return string|null
     */
    private static function getIssuerImage($form, $data)
    {
        if (array_key_exists('logo', $data)) {
            return $data['logo'];
        }

        return $form->issuer_image ?? null;
    }

    /**
     * Compare two quotation objects.
     *
     * @param \App\Models\Form $existingForm Existing form
     * @param array $requestData Request data
     * @return array
     */
    private static function compareQuotations($existingForm, $requestData)
    {
        // Reconstruct request data
        $requestData['issuer_image'] = self::getIssuerImage($existingForm, $requestData);
        $products = $requestData['products'] ?? null;

        // Unset unnecessary data
        unset(
            $requestData['mode'],
            $requestData['type'],
            $requestData['is_supplier_connected'],
            $requestData['supplier'],
            $requestData['logo'],
            $requestData['price'],
            $requestData['products'],
        );

        return $existingForm->compare($requestData, $products);
    }

    /**
     * Compare two purchase order objects.
     *
     * @param \App\Models\Form $existingForm Existing form
     * @param array $requestData Request data
     * @return array
     */
    private static function comparePurchaseOrders($existingForm, $requestData)
    {
        // Reconstruct request data
        $requestData['issuer_image'] = self::getIssuerImage($existingForm, $requestData);
        $products = $requestData['products'] ?? null;

        // Unset unnecessary data
        unset(
            $requestData['mode'],
            $requestData['type'],
            $requestData['is_supplier_connected'],
            $requestData['supplier'],
            $requestData['logo'],
            $requestData['price'],
            $requestData['total_price'],
            $requestData['products'],
        );

        return $existingForm->compare($requestData, $products);
    }

    /**
     * Compare two delivery slip objects.
     *
     * @param \App\Models\Form $existingForm Existing form
     * @param array $requestData Request data
     * @return array
     */
    private static function compareDeliverySlips($existingForm, $requestData)
    {
        // Reconstruct request data
        $requestData['issuer_image'] = self::getIssuerImage($existingForm, $requestData);
        $products = $requestData['products'] ?? null;

        // Unset unnecessary data
        unset(
            $requestData['mode'],
            $requestData['type'],
            $requestData['is_supplier_connected'],
            $requestData['supplier'],
            $requestData['logo'],
            $requestData['price'],
            $requestData['products'],
        );

        return $existingForm->compare($requestData, $products);
    }

    /**
     * Compare two invoice objects.
     *
     * @param \App\Models\Form $existingForm Existing form
     * @param array $requestData Request data
     * @return array
     */
    private static function compareInvoices($existingForm, $requestData)
    {
        // Reconstruct request data
        $products = $requestData['products'] ?? null;
        $requestData += [
            'form_no' => $requestData['invoice_no'],
            'title' => $requestData['subject'],
            'issuer_image' => self::getIssuerImage($existingForm, $requestData),
            'issuer_payment_notes' => $requestData['payment_notes'],
            'issuer_payee_information' => $requestData['payee_information'],
        ];

        // Unset unnecessary data
        unset(
            $requestData['mode'],
            $requestData['type'],
            $requestData['invoice_no'],
            $requestData['subject'],
            $requestData['payment_notes'],
            $requestData['payee_information'],
            $requestData['is_supplier_connected'],
            $requestData['supplier'],
            $requestData['logo'],
            $requestData['price'],
            $requestData['total_price'],
            $requestData['products'],
        );

        return $existingForm->compare($requestData, $products);
    }

    /**
     * Compare two receipt objects.
     *
     * @param \App\Models\Form $existingForm Existing form
     * @param array $requestData Request data
     * @return array
     */
    private static function compareReceipts($existingForm, $requestData)
    {
        // Reconstruct request data
        $requestData['price'] = (float) $requestData['receipt_amount'];

        // Unset unnecessary data
        unset(
            $requestData['mode'],
            $requestData['type'],
            $requestData['is_supplier_connected'],
            $requestData['supplier'],
            $requestData['receipt_amount'],
        );

        return $existingForm->compare($requestData);
    }

    /**
     * Get Quotation form field attributes.
     *
     * @return array
     */
    public static function quotationFormFields()
    {
        return [
            'form_no' => __('Quotation No'),
            'zipcode' => __('Postal code'),
            'address' => __('Business Address'),
            'title' => __('Subject'),
            'issue_date' => __('Date of issue'),
            'expiration_date' => __('Term of validity'),
            'delivery_address' => __('Delivery location'),
            'delivery_date' => __('Delivery deadline'),
            'payment_terms' => __('Payment terms'),
            'issuer_name' => __('Store and trade name'),
            'issuer_department_name' => __('Department Name'),
            'issuer_address' => __('Address'),
            'issuer_tel' => __('Tel'),
            'issuer_fax' => __('Fax'),
            'issuer_business_number' => __('Business number'),
            'issuer_image' => __('Logo'),
            'supplier_name' => __('Supplier'),
            'remarks' => __('Remarks'),
        ];
    }

    /**
     * Get Purchase Order form field attributes.
     *
     * @return array
     */
    public static function purchaseOrderFormFields()
    {
        return [
            'form_no' => __('Purchase Order No'),
            'zipcode' => __('Postal code'),
            'address' => __('Business Address'),
            'title' => __('Subject'),
            'issue_date' => __('Date of issue'),
            'delivery_address' => __('Delivery location'),
            'delivery_date' => __('Delivery deadline'),
            'payment_terms' => __('Payment terms'),
            'remarks' => __('Remarks'),
            'issuer_name' => __('Store and trade name'),
            'issuer_department_name' => __('Department Name'),
            'issuer_address' => __('Business Address'),
            'issuer_tel' => __('Tel'),
            'issuer_fax' => __('Fax'),
            'issuer_business_number' => __('Business number'),
            'issuer_image' => __('Logo'),
            'supplier_name' => __('Supplier'),
        ];
    }

    /**
     * Get Delivery Slip form field attributes.
     *
     * @return array
     */
    public static function deliverySlipFormFields()
    {
        return [
            'form_no' => __('Delivery Number'),
            'zipcode' => __('Postal code'),
            'address' => __('Address'),
            'title' => __('Subject'),
            'issue_date' => __('Date of issue'),
            'delivery_address' => __('Delivery location'),
            'delivery_date' => __('Delivery Date'),
            'delivery_deadline' => __('Delivery deadline'),
            'remarks' => __('Remarks'),
            'issuer_name' => __('Store and trade name'),
            'issuer_department_name' => __('Department Name'),
            'issuer_address' => __('Address'),
            'issuer_tel' => __('Tel'),
            'issuer_fax' => __('Fax'),
            'issuer_business_number' => __('Business number'),
            'issuer_image' => __('Logo'),
            'supplier_name' => __('Supplier'),
        ];
    }

    /**
     * Get Invoice form field attributes.
     *
     * @return array
     */
    public static function invoiceFormFields()
    {
        return [
            'invoice_no' => __('Invoice No'),
            'form_no' => __('Invoice No'),
            'zipcode' => __('Postal code'),
            'address' => __('Business Address'),
            'subject' => __('Subject'),
            'title' => __('Subject'),
            'issue_date' => __('Date of issue'),
            'payment_date' => __('Payment Date'),
            'payment_terms' => __('Payment terms'),
            'remarks' => __('Remarks'),
            'issuer_payment_notes' => __('Payment Notes'),
            'issuer_payee_information' => __('Payee Information'),
            'issuer_name' => __('Store and trade name'),
            'issuer_department_name' => __('Department Name'),
            'issuer_address' => __('Address'),
            'issuer_tel' => __('Tel'),
            'issuer_fax' => __('Fax'),
            'issuer_business_number' => __('Business number'),
            'issuer_image' => __('Logo'),
            'supplier_name' => __('Supplier'),
        ];
    }

    /**
     * Get Receipt form field attributes.
     *
     * @return array
     */
    public static function receiptFormFields()
    {
        return [
            'title' => __('Receipt Title'),
            'issue_date' => __('Date of issue'),
            'receipt_date' => __('Receipt date 2'),
            'refer_receipt_no' => __('Refer Receipt Number'),
            'issuer_name' => __('Store and trade name'),
            'issuer_address' => __('Address'),
            'issuer_tel' => __('Tel'),
            'issuer_business_number' => __('Business number'),
            'supplier_name' => __('Supplier'),
            'price' => __('Receipt Amount'),
            'receipt_amount' => __('Receipt Amount'),
        ];
    }
}
