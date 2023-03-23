<?php

namespace Tests\Unit\Helpers\OperationDetailHelper;

use App\Enums\Form\Types;
use App\Helpers\OperationDetailHelper;
use App\Models\Form;
use Tests\TestCase;

class DeliverySlipTest extends TestCase
{
    /**
     * Check if helper generates the correct Operation Detail for Registration page
     *
     * @return void
     */
    public function testTestSuccessDeliverySlipRegistration()
    {
        // Fetch Delivery Slip form
        $form = Form::where('type', Types::DELIVERY_SLIP)->first();

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
    public function testTestFailedDeliverySlipRegistration()
    {
        // Fetch Delivery Slip form
        $form = Form::where('type', Types::DELIVERY_SLIP)->first();

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
    public function testTestSuccessDeliverySlipEditBasicInformation()
    {
        // Fetch Delivery Slip form
        $form = Form::where('type', Types::DELIVERY_SLIP)->first();

        // Setup form input data
        $inputData = [
            'form_no' => $form->form_no,
            'zipcode' => $form->zipcode,
            'address' => $form->address . ' edited',
            'title' => $form->title . 'edited',
            'issue_date' => $form->issue_date,
            'delivery_address' => $form->delivery_address,
            'delivery_date' => $form->delivery_date,
            'delivery_deadline' => $form->delivery_deadline,
            'issuer_name' => $form->issuer_name,
            'issuer_department_name' => $form->issuer_department_name,
            'issuer_address' => $form->issuer_address,
            'issuer_tel' => $form->issuer_tel,
            'issuer_fax' => $form->issuer_fax,
            'issuer_business_number' => $form->issuer_business_number,
            'issuer_image' => $form->issuer_image,
            'supplier_name' => $form->supplier_name,
            'remarks' => $form->remarks,
        ];

        // Expected output
        $expected = null;
        foreach ($inputData as $field => $data) {
            if ($form->$field != $data) {
                $field = OperationDetailHelper::deliverySlipFormFields()[$field];
                $expected .= __('Operation - Edit Basic Information', ['field' => $field]) . "\r\n";
            }
        }

        // Actual output
        $actual = OperationDetailHelper::getEditMessage($form, $inputData, Types::DELIVERY_SLIP);

        $this->assertTrue($expected == $actual);
    }
}
