@extends('layouts.main')

@section('content')
@include('layouts.components.neo.participation_status_modal');
<div class="container position-relative pb-4 mb-md-3 home pt-6 home--height">
    <div class="row">
        <div class="col-12 col-md-9 offset-md-3">
            <div class="d-flex flex-column rounded-3 ">
                <div class="border-bottom position-relative">
                    <a href="{{ route('neo.profile.introduction', $neo->id) }}" class="btn btn-secondary btn--back">
                        <i class="ai-arrow-left"></i>
                    </a>
                    <h3 class="p-3 mb-0 text-center">{{__('Participation application/invitation management') }}</h3>
                </div>
                <div class="text-end mt-2">
                    <a href="{{ route('neo.profile.participation-invitation', $neo->id) }}" class="btn btn-link">
                        <i class="ai-plus me-1"></i>
                        {{ __('Invite RIO') }}
                    </a>
                </div>
                <div class="mt-4">
                    {{-- Section loader --}}
                    @include('components.section-loader', ['show' => false])
                    @include('layouts.components.neo.participation_invitation_management_links', ['active' => __('Application for participation')])
                    @include('neo.profile.pending-participants')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(function () {
        //Approve or Reject participants
        $(document).off('click', '.manage-btn').on('click', '.manage-btn', function(event){
            const loader = $('.js-section-loader');
            // Start loader
            loader.removeClass('d-none');
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var rioId = $(this).attr('data-rio-id');
            var fullname = $(this).attr('data-user-name');
            var id = $(this).attr('data-id');
            var status = $(this).attr('data-status');
            var url 
            if (status === 'Approve') {
              url = "{{ route('neo.profile.participant-management.approve-participant') }}"
            } else {
              url = "{{ route('neo.profile.participant-management.reject-participant') }}"
            }
            $('#manage-participant-btn *').attr('disabled', true);
            fetch(url, {
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json, text-plain, */*",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": csrfToken
                },
                method: 'PATCH',
                credentials: "same-origin",
                body: JSON.stringify({
                  id: id,
                  rio_id: rioId,
                })
            })
            .then(response => {
                if (response.ok) {
                    loader.addClass('d-none');
                    $("#participant-name").text(fullname);
                    $('#dialogue').modal('show')
                    return response.json();
                }
            })
            .then(data => {
                if (data) {
                    $("#participant-name").text(fullname);
                    $("#participant-status").text(data.data);
                    $("#pending-participants").load(" #pending-participants > *");
                } else {
                    window.location.reload();
                }
            })
            .catch(function(error) {
                console.log(error);
                loader.addClass('d-none');
            })
        })
        
        //Pending participants pagination
        $(document).off('click', '#pending-participants-pagination .pagination a').on('click', '#pending-participants-pagination .pagination a', function(event){
            event.preventDefault(); 
            const loader = $('.js-section-loader');
            // Start loader
            loader.removeClass('d-none');
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var page = $(this).attr('href').split('page=')[1];
            var url = "{{ route('neo.profile.participant-management.pending-participants', $neo->id) }}";
            $('.page-item').addClass('disabled');
            fetch(url, {
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json, text-plain, */*",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": csrfToken
                },
                method: 'PATCH',
                credentials: "same-origin",
                body: JSON.stringify({
                    pageNo: page,
                })
            })
            .then(response => {
                if (response.ok) {
                    loader.addClass('d-none');
                    return response.text();
                }
            })
            .then(data => {
                if (data) {
                    $("#pending-participants").html(data);
                }
            })
            .catch(function(error) {
                console.log(error);
                loader.addClass('d-none');
            })
        });
    });
</script>
@endpush
