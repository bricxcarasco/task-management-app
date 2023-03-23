@extends('layouts.main')

@section('content')
    <invite-members-section :group='@json($group)' :rio='@json($rio)'>
        <template v-slot:header>
            {{-- Connection name & tabs section --}}
            @include('connection.components.name-tabs-section', [
                'fullname' => $rio->full_name,
                'active_tab' => 'group'
            ])

            {{-- Redirect to connection group list --}}
            <div class="position-relative mb-4">
                <a href="{{ route('connection.groups.index') }}" class="btn btn-link">
                    <i class="ai-arrow-left"></i>
                    {{ __('Back to the connection group list page') }}
                </a>
            </div>
        </template>
    </invite-members-section>
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/connection-group/index.js') }}" defer></script>
@endpush
