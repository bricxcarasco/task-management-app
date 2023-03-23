@extends('layouts.main')

@section('content')
    <workflow-list :service='@json($service)' />
@endsection

@push('css')
<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('vuejs')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
    <script src="{{ mix('js/dist/fileuploader-facade.js') }}" defer></script>
    <script src="{{ mix('js/dist/workflows/index.js') }}" defer></script>
@endpush
