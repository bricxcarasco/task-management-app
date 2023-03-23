@php
    use App\Enums\Classified\SaleProductVisibility;
    use App\Enums\ServiceSelectionTypes;
@endphp

@include('components.alert')

<div class="
    container
    position-relative
    zindex-5
    pt-6
    py-md-6
    mb-md-3
    home--height
  ">
    <div class="row">
        <!-- Content-->
        <div class="col-12 col-md-9 offset-md-3">
            <div class="d-flex flex-column pb-4 pb-md-0 rounded-3">
                <div class="position-relative">
                    <h3 class="py-3 mb-0 text-center">
                        {{ $service->type === ServiceSelectionTypes::RIO ? $service->data->full_name : $service->data->organization_name}}
                    </h3>
                </div>
                <div>
                    <a href="{{ route('classifieds.sales.registered') }}">
                        <i class="ai-arrow-left me-2"></i>{{ __('Return to product list') }}
                    </a>
                </div>
                <h5 class="text-center">
                    {{ __('Product information input') }}
                </h5>
            </div>
            <div class="mb-3">
                {{-- Product Image Uploader --}}
                <label for="text-input" class="form-label">
                    {{ __('Product image')}}
                </label>
                {{-- File Uploader --}}
                <div class="file-uploader file-uploader--grid col-12 d-none">
                    <input type="file" class="js-product-file-uploader" name="upload_file[]"
                        data-max-file-size="{{ config('bphero.classified_sales_max_file_size') }}"
                        data-max-files="{{ config('bphero.classified_sales_max_files') }}"
                        data-upload="{{ route('classifieds.sales.registered-products.images.process-upload') }}"
                        data-chunk="{{ route('classifieds.sales.registered-products.images.chunk') }}"
                        data-revert="{{ route('classifieds.sales.registered-products.images.revert') }}"
                        data-restore="{{ route('classifieds.sales.registered-products.images.restore') }}"
                        data-load="{{ route('classifieds.sales.registered-products.images.load') }}" />
                    <span class="js-error-max-files d-none">{{ __('Maximum upload files reached', ['count' => config('bphero.classified_sales_max_files')]) }}</span>
                </div>
                {{-- Limbo Files --}}
                <div class="js-product-file-uploader-temp-files d-none">
                    @foreach (old('upload_file', $product->upload_file ?? []) as $code)
                        @if (!empty($code))
                            <input type="hidden" value="{{ $code }}">
                        @endif
                    @endforeach
                </div>
                {{-- Local Files --}}
                <div class="js-product-file-uploader-local-files d-none">
                    @foreach (old('local_file', $product->local_file ?? $product->image_paths ?? []) as $path)
                        @if (!empty($path))
                            <input type="hidden" value="{{ rawurlencode($path) }}">
                        @endif
                    @endforeach
                </div>
                <div class="col-12 text-center">
                    <button type="button" class="btn btn-outline-primary js-product-upload-button">
                        {{ __('Register the image') }}
                    </button>
                </div>
            </div>
            <div class="mb-3">
                {{-- Product Category --}}
                <label for="select-input" class="form-label">
                    {{ __('Product category') }}
                </label>
                <select class="form-select @error('sale_category') is-invalid @enderror"
                    id="select-input"
                    name="sale_category"
                >
                    @foreach ($productCategory as $category)
                        <option value="{{ $category->id }}"
                            @if(!empty($isProductSession) && $category->id ==  $product->sale_category)
                                selected
                            @elseif($errors->any() && old('sale_category') == $category->id)
                                selected
                            @elseif(!$errors->any() && old('sale_category') == $category->id)
                                selected
                            @elseif(!$errors->any() && $category->id == $product->sale_category)
                                selected
                            @elseif($errors->any() && old('sale_category') != $category->id && $category->id == $product->sale_category)
                                selected
                            @endif
                        >{{ $category->sale_category_name }}</option>
                    @endforeach
                </select>
                @error('sale_category')
                    <div class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>
            <div class="mb-3">
                {{-- Product Name --}}
                <label for="text-input" class="form-label">
                    {{ __('Product name')}}
                </label>
                <span class="text-danger">*</span>
                <input class="form-control @error('title') is-invalid @enderror"
                    type="text"
                    id="text-input"
                    name="title"
                    value="{{ $errors->any() ? old('title') : $product->title }}" />
                @error('title')
                    <div class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>
            <div class="mb-3">
                {{-- Price (Tax/Shipping) --}}
                <label for="number-input" class="form-label">
                    {{ __('Price tax shipping') }}
                </label>
                <span class="text-danger">*</span>
                <div class="position-relative form--number">
                    <input class="form-control @error('price') is-invalid @enderror input-icon classsified-create-price"
                        type="number"
                        id="price-quote" name="price"
                        value="{{ $errors->any() ? old('price') : (isset($product->price) ? intval($product->price) : null) }}"
                        @if(!empty($isProductSession) && empty($product->price))
                            disabled
                        @elseif(isset($product->id) && empty($product->price) && empty(old('price')))
                            disabled
                        @elseif(old('set_quote'))
                            disabled
                        @endif
                    />
                </div>
                @error('price')
                    <div class="invalid-feedback d-block">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="set-quote" name="set_quote"
                        @if(!empty($isProductSession) && empty($product->price))
                            checked
                        @elseif(isset($product->id) && empty($product->price) && empty(old('price')))
                            checked
                        @elseif(old('set_quote'))
                            checked
                        @endif
                    />
                    <label class="form-check-label" for="set-quote">
                        {{ __('Set price to individual quote') }}
                    </label>
                </div>
            </div>
            <div class="mb-3">
                {{-- Product Description --}}
                <label for="textarea-input" class="form-label">
                    {{ __('Product description') }}
                </label>
                <span class="text-danger">*</span>
                <textarea class="form-control @error('detail') is-invalid @enderror"
                    id="product-detail"
                    rows="5"
                    maxlength="1000" name="detail"
                    value="{{ $errors->any() ? old('detail') : $product->detail }}"
                >{{ $errors->any() ? old('detail') : $product->detail }}</textarea>
                <p class="text-end">
                    <span id="textCount"></span>/<span id="maxCount"></span>
                </p>
                @error('detail')
                    <div class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>
            <div class="mb-3">
                {{-- Publising Settings --}}
                <label for="select-input" class="form-label">
                    {{ __('Publishing Settings') }}
                </label>
                <select class="form-select @error('is_public') is-invalid @enderror" id="select-input" name="is_public">
                    @foreach ($visibility as $index => $visibility)
                        <option value="{{ $index }}"
                            @if(!empty($isProductSession) && $product->is_public == $index)
                                selected
                            @elseif(empty($isProductSession) && is_null(old('is_public')) && !isset($product->is_public) && $index == SaleProductVisibility::IS_PUBLIC)
                                selected
                            @elseif($errors->any() && old('is_public') == $index)
                                selected
                            @elseif(!$errors->any() && old('is_public') == $index)
                                selected
                            @elseif(!$errors->any() && $product->is_public == $index)
                                selected
                            @elseif($errors->any() && old('is_public') != $index && $product->is_public == $index)
                                selected
                            @endif
                        >
                            {{ $visibility }}
                        </option>
                    @endforeach
                </select>
                @error('is_public')
                    <div class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>
            <p class="p-3 w-50 mx-auto mt-5 mb-0 write-legal-terms" style="border: 1px dashed #ddd;">
                {!! __('Please make sure that you do not violate the EC Terms of Service') !!}
                <br><br>
                {{ __('If the exhibit falls under the specified continuous service, please fulfill the obligations required by the Specified Commercial Transactions Law before listing.') }}
            </p>
            <div class="text-center mt-4 mb-5">
                <button type="submit" class="btn btn-primary js-product-submit" id="confirm">
                    {{ __('Confirmation of registration information') }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('css')
<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
@endpush

@push('js')
<script src="{{ mix('js/dist/fileuploader-facade.js') }}" defer></script>
<script>
$(document).ready(function() {
    var detail = $("#product-detail").val();
    $("#maxCount").text(1000);

    if (detail) {
      $("#textCount").text(detail.length);
    } else {
      $("#textCount").text(0);
    }

    //Disable price input
    $("#set-quote").change(function (e) {
      if($('#set-quote').prop('checked')) {
        $('#price-quote').val('');
        $('#price-quote').prop('disabled', true);
        $('#set-quote').prop('value', true);
      } else {
        $('#price-quote').prop('disabled', false);
        $('#set-quote').prop('value', false);
      }
    });

    //Price counter
    $("#product-detail").on("input", function(e) {
      $("#textCount").text(this.value.length);

      if(this.value.length === 1000) {
        $("#product-detail").onkeydown = function (e) {
            e.preventDefault();
        }
      }
    });

    /**
     * Initialize file uploader
     */
    let fileUploader = FileUploaderFacade({
        selector: '.js-product-file-uploader',
        codesSelector: '.js-product-file-uploader-temp-files',
        pathsSelector: '.js-product-file-uploader-local-files',
        hasImagePreview: true,
        allowMultiple: true,
        chunkUploads: true,
        instantUpload: true,
        allowReorder: true,
        allowProcess: false,
        styleItemPanelAspectRatio: '0.75',
        labelIdle: '',
        acceptedFileTypes: [
            'image/jpeg',
            'image/jpg',
            'image/pjpeg',
            'image/png',
        ],
        fileValidateTypeLabelExpectedTypesMap: {
            'image/jpeg': '.jpeg',
            'image/jpg': '.jpg',
            'image/pjpeg': null,
            'image/png': '.png',
        }
    });

    /**
     * Change disabled state of form submit button
     *
     * @param {boolean} state
     * @returns {void}
     */
    let toggleProductSubmit = function (state) {
        $('.js-product-submit').prop('disabled', state);
    }

    /**
     * Event listener for when adding or removing files in uploader
     */
    fileUploader.pond.on('updatefiles', function (files) {
        console.log(files);
        let hasFiles = (files.length > 0);

        // Display file uploader UI
        $('.file-uploader').toggleClass('d-none', !hasFiles);

        // Enable when no file
        if (!hasFiles) {
            toggleProductSubmit(false);

            return;
        }

        // Enable submit button when all file process is complete
        let isProcessComplete = fileUploader.isProcessComplete();
        toggleProductSubmit(!isProcessComplete);
    });

    /**
     * Event listener for max files reached
     */
    fileUploader.pond.on('warning', function (error) {
        if (error.body === 'Max files') {
            const message = $('.js-error-max-files').text() ?? '';
            setAlert('danger', message);
        }
    });

    /**
     * Event listener for when upload file has finished uploading
     */
    fileUploader.pond.on('processfile', function () {
        // Enable submit button when all file process is complete
        let isProcessComplete = fileUploader.isProcessComplete();
        toggleProductSubmit(!isProcessComplete);
    });

    /**
     * Event listener for browsing files through button
     */
    $(document).on('click', '.js-product-upload-button', function () {
      fileUploader.pond.browse();
    })

    /**
     * Event listener for preventing form submission on invalid uploads
     */
    $(document).on('submit', '.js-product-form', function (event) {
        if (!fileUploader.isProcessComplete()) {
            event.preventDefault();
        }
    })
});
</script>
@endpush