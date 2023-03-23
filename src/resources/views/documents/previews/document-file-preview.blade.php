@extends('layouts.main')

@section('content')

<div class="container position-relative zindex-5 pt-6 py-md-6 mb-md-3 home--height">
  <div class="row">
    <!-- Content-->
    <div class="col-12">
      <div class="d-flex flex-column h-100 rounded-3 ">
        <div class="position-relative">
          <h3 class="py-3 mb-0 text-center">
            {{ __('User document header', [
              'fullname' => $service->data->full_name ?? $service->data->organization_name,
            ]) }}
          </h3>
        </div>

        <div class="text-center position-relative p-2  mb-2">
          @if(is_null($fileDetails->directory_id))
            @if(!isset($requestData['shared_folder']))
            <a href="{{ route('document.default-list') }}">
            @else
            <a href="{{ route('document.shared-list') }}">
            @endif
              <i class="ai-arrow-left message__back"></i>
            </a>
          @else
            @if(!isset($requestData['shared_folder']))
            <a href="{{ route('document.folder-file-list', $fileDetails->directory_id) }}">
            @else
            <a href="{{ route('document.shared-folder-file-list', $fileDetails->directory_id) }}">
            @endif
              <i class="ai-arrow-left message__back"></i>
            </a>
          @endif
          <strong>
            {{ $fileDetails->document_name }}
          </strong>
        </div>

        <file-preview
            :service='@json($service)'
            :file_data='@json($fileDetails)'
            :id='@json($fileId)'>
        </file-preview>
      </div>
    </div>
  </div>
</div>

@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/document-management/index.js') }}" defer></script>
@endpush