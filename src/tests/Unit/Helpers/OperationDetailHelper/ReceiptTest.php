<?php

namespace Tests\Unit\Helpers\OperationDetailHelper;

use App\Enums\Form\Types;
use App\Helpers\OperationDetailHelper;
use App\Models\Form;
use Tests\TestCase;

class ReceiptTest extends TestCase
{
    /**
     * Check if helper generates the correct Operation Detail for Registration page
     *
     * @return void
     */
    public function testTestSuccessReceiptRegistration()
    {
        // Fetch Receipt form
        $form = Form::where('type', Types::RECEIPT)->first();

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
    public function testTestFailedReceiptRegistration()
    {
        // Fetch Receipt form
        $form = Form::where('type', Types::RECEIPT)->first();

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
    public function testTestSuccessReceiptEditBasicInformation()
    {
        // Fetch Receipt form
        $form = Form::where('type', Types::RECEIPT)->first();

        // Setup form input data
        $inputData = [
            'title' => $form->title . 'edited',
            'issue_date' => $form->issue_date,
            'receipt_date' => $form->receipt_date,
            'refer_receipt_no' => $form->refer_receipt_no,
            'issuer_name' => $form->issuer_name,
            'issuer_address' => $form->issuer_address,
            'issuer_tel' => $form->issuer_tel,
            'issuer_business_number' => $form->issuer_business_number,
            'supplier_name' => $form->supplier_name,
            'receipt_amount' => $form->price,
        ];

        // Expected output
        $expected = null;
        foreach ($inputData as $field => $data) {
            if ($form->$field != $data) {
                $field = OperationDetailHelper::receiptFormFields()[$field];
                $expected .= __('Operation - Edit Basic Information', ['field' => $field]) . "\r\n";
            }
        }

        // Actual output
        $actual = OperationDetailHelper::getEditMessage($form, $inputData, Types::RECEIPT);

        $this->assertTrue($expected == $actual);
    }
}
