@php
  use App\Enums\Form\Types;
@endphp

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link type="text/css" rel="stylesheet" href="css/pdf.css">
  </head>
  <body>
    <main>
        {{-- PDF Download content --}}
        @switch ($form->type)
          @case(Types::RECEIPT)
            @include('forms.components.pdf-download-receipt')
            @break 
          @case(Types::INVOICE)
            @include('forms.components.pdf-download-invoice')
            @break
          @case(Types::PURCHASE_ORDER)
            @include('forms.components.pdf-download-purchase-order')
            @break
          @case(Types::DELIVERY_SLIP)
            @include('forms.components.pdf-download-delivery-slip')
            @break
          @default
            @include('forms.components.pdf-download-quotation')
            @break
        @endswitch
    </main>
  </body>
</html>