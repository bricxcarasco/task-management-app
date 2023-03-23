<?php

namespace Tests\Unit\Helpers\OperationDetailHelper;

use App\Enums\Form\Types;
use App\Helpers\OperationDetailHelper;
use App\Models\Form;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    /**
     * Check if helper generates the correct Operation Detail for Registration page
     *
     * @return void
     */
    public function testTestSuccessInvoiceRegistration()
    {
        // Fetch Invoice form
        $form = Form::where('type', Types::INVOICE)->first();

        // Expected & actual outputs
        $expected = __('Operation - Registration', ['title' => $form->title]);
        $actual = OperationDetailHelper::getRegistrationMessage($form->title);

        $this->assertTrue($expected == $actual);
    }

    /**
     * Check if helper generates incorrect Operation Detail for Registration page
     *
     * @return void
     */
    public function testTestFailedInvoiceRegistration()
    {
        // Fetch Invoice form
        $form = Form::where('type', Types::INVOICE)->first();

        // Expected & actual outputs
        $expected = __('Operation - Registration', ['title' => $form->title]);
        $actual = 'wrong actual output';

        $this->assertFalse($expected == $actual);
    }

    /**
     * Check if helper generates the correct Operation Detail - Basic Information for Edit page
     *
     * @return void
     */
    public function testTestSuccessInvoiceEditBasicInformation()
    {
        // Fetch Invoice form
        $form = Form::where('type', Types::INVOICE)->first();

        // Setup form input data
        $inputData = [
            'invoice_no' => $form->form_no . '11',
            'subject' => $form->title . ' edited',
            'zipcode' => $form->zipcode,
            'address' => $form->address,
            'issue_date' => $form->issue_date,
            'payment_date' => $form->payment_date,
            'payment_terms' => $form->payment_terms,
            'issuer_name' => $form->issuer_name,
            'issuer_department_name' => $form->issuer_department_name,
            'issuer_address' => $form->issuer_address,
            'issuer_tel' => $form->issuer_tel,
            'issuer_fax' => $form->issuer_fax,
            'issuer_business_number' => $form->issuer_business_number,
            'issuer_image' => $form->issuer_image,
            'supplier_name' => $form->supplier_name,
            'remarks' => $form->remarks,
            'payment_notes' => $form->issuer_payment_notes,
            'payee_information' => $form->issuer_payee_information,
        ];

        // Expected output
        $expected = null;
        foreach ($inputData as $field => $data) {
            if ($form->$field != $data) {
                $field = OperationDetailHelper::invoiceFormFields()[$field];
                $expected .= __('Operation - Edit Basic Information', ['field' => $field]) . "\r\n";
            }
        }

        // Actual output
        $actual = OperationDetailHelper::getEditMessage($form, $inputData, Types::INVOICE);

        $this->assertTrue($expected == $actual);
    }
}
