@php
  use App\Enums\ServiceSelectionTypes;
@endphp
@extends('layouts.main')

@section('content')
<div class="container position-relative zindex-5 pt-6 py-md-6 mb-md-3 home--height">
    {{-- Page Loader --}}
    @include('components.section-loader')
    <div class="row">
        <!-- Content-->
        <div class="col-12 col-md-9 offset-md-3">
        <div class="d-flex flex-column pb-4 pb-md-0 rounded-3 ">
            <div class="position-relative">
                <h3 class="py-3 mb-0 text-center"> {{ $service->type === ServiceSelectionTypes::RIO ? $service->data->full_name : $service->data->organization_name}}</h3>
            </div>
            <div>
            <a class="btn btn-link" href="{{ route('classifieds.sales.create') }}"><i class="ai-arrow-left me-2"></i>{{ __('Return to the Input Screen') }}</a>
            </div>
            <h5 class="text-center">{{ __('Product Information') }}</h5>
        </div>
        <form method="POST" action="{{ route('classifieds.sales.complete') }}" novalidate>
            @csrf
            @method('POST')
            <div class="mb-3">
                {{-- Product Image Uploader --}}
                <label for="text-input" class="form-label">
                    {{ __('Product image')}}
                </label>
                {{-- File Uploader --}}
                <div class="file-uploader file-uploader--grid col-12 d-none">
                    <input type="file" class="js-product-file-uploader" data-restore="{{ route('classifieds.sales.registered-products.images.restore') }}" />
                </div>
                {{-- Limbo Files --}}
                <div class="js-product-file-uploader-temp-files d-none">
                    @foreach ($product['upload_file'] as $code)
                        @if (!empty($code))
                            <input type="hidden" value="{{ $code }}">
                        @endif
                    @endforeach
                </div>
                {{-- Local Files --}}
                <div class="js-product-file-uploader-local-files d-none">
                    @foreach ($product->image_paths ?? [] as $path)
                        @if (!empty($path))
                            <input type="hidden" value="{{ rawurlencode($path) }}">
                        @endif
                    @endforeach
                </div>
                {{-- No uploaded image/s --}}
                @if (empty($product['upload_file'] ?? []) && empty($product->image_paths ?? []))
                    <p class="ps-3 text-center border py-3">{{ __('No product images to display') }}</p>
                @endif
            </div>
            <div class="mb-3">
                <label for="select-input" class="form-label">{{ __('Product category') }}</label>
                <p class="ps-3">{{ sale_category($product['sale_category']) }}</p>
            </div>
            <div class="mb-3">
                <label for="text-input" class="form-label">{{ __('Product name') }}</label>
                <p class="ps-3">{{ $product['title'] }}</p>
            </div>
            @if(isset($product['price']))
                <div class="mb-3">
                    <label for="number-input" class="form-label">{{ __('Price tax shipping') }}</label>
                        <p class="ps-3">{{ $product['price'] }}円 {{ __('Tax shipping included') }}</p>
                </div>
            @endif
            <div class="mb-3">
                <label for="textarea-input" class="form-label">{{ __('Product description') }}</label>
                <p class="ps-3">{{ $product['detail'] }}</p>
            </div>
            <div class="mb-3">
                <label for="select-input" class="form-label">{{ __('Publishing Settings') }}</label>
                <p class="ps-3">{{ publishing_setting($product['is_public']) }}</p>
            </div>
            <p class="p-3 w-50 mx-auto mt-5 mb-0 write-legal-terms" style="border: 1px dashed #ddd;">
                {!! __('Please make sure that you do not violate the EC Terms of Service') !!}
                <br><br>
                {{ __('If the exhibit falls under the specified continuous service, please fulfill the obligations required by the Specified Commercial Transactions Law before listing.') }}
            </p>
            <div class="text-center mt-4 mb-5">
                <button type="submit" class="btn btn-primary">{{ __('Classified Confirmation of registration information') }}</button>
            </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('css')
<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
@endpush

@push('js')
<script src="{{ mix('js/dist/fileuploader-facade.js') }}" defer></script>
<script>
  $(document).ready(function() {
    let buttonSelector = $("button[type=submit]");
    
    $(document).on('submit', 'form', function() {
        $('.js-section-loader').removeClass('d-none');
    })

    /**
     * Initialize file uploader
     */
    let fileUploader = FileUploaderFacade({
      selector: '.js-product-file-uploader',
      codesSelector: '.js-product-file-uploader-temp-files',
      hasImagePreview: true,
      allowMultiple: true,
      instantUpload: false,
      allowDrop: false,
      allowBrowse: false,
      allowPaste: false,
      allowProcess: false,
      allowRemove: false,
      allowReplace: false,
      allowRevert: false,
      imagePreviewHeight: 150,
      labelIdle: '',
      labelTapToUndo: '',
      labelFileProcessingComplete: '',
    });

    /**
     * Event listener for when adding or removing files in uploader
     */
     fileUploader.pond.on('updatefiles', function (files) {
        let hasFiles = (files.length > 0);

        // Display file uploader UI
        $('.file-uploader').toggleClass('d-none', !hasFiles);
    });
});
</script>
@endpush