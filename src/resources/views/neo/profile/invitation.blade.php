@extends('layouts.main')

@section('content')
@include('layouts.components.neo.invitation_cancelled_modal');
 <div class="container position-relative zindex-5 pb-4 mb-md-3 home--height pt-6">
    <div class="row">
        <div class="col-12 offset-md-3 col-md-9">
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
                    @include('layouts.components.neo.participation_invitation_management_links', ['active' => __('Inviting')])
                    @include('neo.profile.invitation-list', ['connected' => $connectedLists])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script>
    $(function () {
        var currentPage = null;
        //Cancel invitation
        $(document).off('click', '#cancel-invitation').on('click', '#cancel-invitation', function(event) {
            event.preventDefault(); 
            const loader = $('.js-section-loader');
            // Start loader
            loader.removeClass('d-none');
            $(this).addClass("disabled");
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var fullname = $(this).attr('data-user-name');
            var id = $(this).attr('data-id');
            fetch("{{ route('neo.profile.invitation-management.cancel-invitation', $neo->id) }}", {
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
                  pageNo: currentPage,
                })
            })
            .then(response => {
                if (response.ok) {
                    loader.addClass('d-none');
                    $("#user-name").text(fullname);
                    $('#invitation-cancelled').modal('show')
                    return response.text();
                }
            })
            .then(data => {
                if (data) {
                    $("#user-name").text(fullname);
                    $("#invitation-lists").html(data);
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
        $(document).off('click', '#invitation-pagination a').on('click', '#invitation-pagination a', function(event){
            event.preventDefault(); 
            const loader = $('.js-section-loader');
            // Start loader
            loader.removeClass('d-none');
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var page = $(this).attr('href').split('page=')[1];
            var url = "{{ route('neo.profile.invitation-management.invitation-lists', $neo->id) }}";
            $('#invitation-pagination .page-item').addClass('disabled');
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
                    currentPage = page;
                    loader.addClass('d-none');
                    return response.text();
                }
            })
            .then(data => {
                if (data) {
                    $("#invitation-lists").html(data);
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