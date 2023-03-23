<p class="mb-0 bg-dark-gray p-2 c-white">{{ __('Issuer Information') }}</p>
<div class="p-4">
    <table class="table table-striped table--quotation">
        <tbody>
            <tr class="bg-blue">
                <th>{{ __('Store name / Trade name') }}</th>
                <td>{{ $form->issuer_name }}</td>
            </tr>
            <tr>
                <th>{{ __('Department Name') }}</th>
                <td>{{ $form->issuer_department_name }}</td>
            </tr>
            <tr>
                <th>{{ __('Address2') }}</th>
                <td>{{ $form->issuer_address }}</td>
            </tr>
            <tr>
                <th>TEL</th>
                <td>{{ $form->issuer_tel }}</td>
            </tr>
            <tr>
                <th>FAX</th>
                <td>{{ $form->issuer_fax }}</td>
            </tr>
            <tr>
                <th>{{ __('Business Number') }}</th>
                <td>{{ $form->issuer_business_number }}</td>
            </tr>
            <tr>
                <th>{{ __('Logo') }}</th>
                <td>
                    @if ($form->issuer_detail_logo !== null)
                        <img class="d-block" src="{{ $form->issuer_detail_logo }}"
                            onerror="this.src = '{{ asset('img/default-image.png') }}'" alt="{{ $form->issuer_name }}"
                            width="110">
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>
