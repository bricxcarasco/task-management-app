@php
    use App\Enums\ServiceSelectionTypes;
@endphp
<div id="service-selection-list">
    @csrf
    @if(!($pageNo ?? null) || ($pageNo ?? null) == 1)
        <div class="switch-default border-bottom py-3">
            <input type="radio" name="radio" class="service-selection" value="RIO" id="user-{{ $currentUser->id }}" {{ $serviceSelected->type === ServiceSelectionTypes::RIO ? "checked" : ""}}/>
            <label class="form-check-label" for="user-{{ $currentUser->id }}">{{ $currentUser->family_name.' '.$currentUser->first_name }} (RIO)</label>
        </div>
    @endif
    @foreach ($neos as $neo)
        <div class="switch-default border-bottom py-3">
            <input type="radio" name="radio" class="service-selection" value="NEO" id="neo-{{ $neo->id }}" 
            {{ $serviceSelected->type === ServiceSelectionTypes::NEO && $serviceSelected->data->id === $neo->id ? "checked" : "" }}/>
            <label class="form-check-label" for="neo-{{ $neo->id }}">{{ $neo->organization_name }}</label>
        </div>
    @endforeach
    <div class="d-flex justify-content-center mt-1" id="service-selection-pagination">
        {!! $neos->links() !!}
    </div> 
</div>

@push('js')
<script>
$(function () {
    $(document).off('click', '.service-selection').on('click', '.service-selection', function(event){
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        //Get all integers
        let convertId = this.id.replace(/[^0-9]/g,'');
        currentPage = window.location.pathname;
        let url
        switch(this.value) {
            case 'NEO':
                url = "{{ route('neo.profile.update.service-selection') }}"
                break;
            case 'RIO':
                url = "{{ route('rio.profile.update.service-selection') }}"
                break;
            default:
        }
        fetch(url, {
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json, text-plain, */*",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": csrfToken
            },
            method: 'POST',
            credentials: "same-origin",
            body: JSON.stringify({
                id: convertId,
            })
        })
        .then(response => {
            if (response.ok) {
                $('#service_switch').modal('hide')
                return response.json();
            }
        })
        .then(data => {
            if(data){
                // Check current page is not homepage
                if(currentPage === '/'){
                    //Update homepage data base on selected service
                    //PC
                    $("#service-selected-name").html(data.data.name);
                    $("#current-service-selected").attr("href", data.data.link);
                    //SP
                    $("#service-name").html(data.data.name);
                    $("#current-service").attr("href", data.data.link);
                
                    //Update PC and SP sidebar base on selected service
                    //PC
                    $("#pc-sidebar-current-service-profile-picture").attr("src", data.data.profile_image);
                    $("#pc-sidebar-current-service-profile-picture").attr("alt", data.data.name);
                    $("#pc-sidebar-current-service-name").html(data.data.name);
                    $("#pc-sidebar-current-service-profile-edit").attr("href", data.data.profile_edit_link);
                    $("#pc-sidebar-current-service-privacy-edit").attr("href", data.data.privacy_edit_link);

                    //SP
                    $("#sp-sidebar-current-service-profile-picture").attr("src", data.data.profile_image);
                    $("#sp-sidebar-current-service-profile-picture").attr("alt", data.data.name);
                    $("#sp-sidebar-current-service-name").html(data.data.name);
                    $("#sp-sidebar-current-service-profile-edit").attr("href", data.data.profile_edit_link);
                    $("#sp-sidebar-current-service-privacy-edit").attr("href", data.data.privacy_edit_link);

                    switch(this.value) {
                        case 'NEO':
                            //PC
                            $("#pc-sidebar-current-service-information-edit").addClass('d-none');
                            $("#pc-sidebar-current-service-others-header").addClass('d-none');
                            $("#pc-sidebar-current-service-introduce-hero").addClass('d-none');
                            $("#pc-sidebar-current-service-basic-settings").addClass('d-none');

                            //SP
                            $("#sp-sidebar-current-service-information-edit").addClass('d-none');
                            $("#sp-sidebar-current-service-others-header").addClass('d-none');
                            $("#sp-sidebar-current-service-introduce-hero").addClass('d-none');
                            $("#sp-sidebar-current-service-basic-settings").addClass('d-none');
                            break;
                        case 'RIO':
                            //PC
                            $("#pc-sidebar-current-service-information-edit").removeClass('d-none');
                            $("#pc-sidebar-current-service-others-header").removeClass('d-none');
                            $("#pc-sidebar-current-service-introduce-hero").removeClass('d-none');
                            $("#pc-sidebar-current-service-basic-settings").removeClass('d-none');

                            //SP
                            $("#sp-sidebar-current-service-information-edit").removeClass('d-none');
                            $("#sp-sidebar-current-service-others-header").removeClass('d-none');
                            $("#sp-sidebar-current-service-introduce-hero").removeClass('d-none');
                            $("#sp-sidebar-current-service-basic-settings").removeClass('d-none');
                            break;
                        default:
                    }
                }
                window.location.replace("{{route('home')}}");
            }
        })
        .catch(function(error) {
            console.log(error);
        });
    });

    //Service selections pagination
    $(document).off('click', '#service-selection-pagination a').on('click', '#service-selection-pagination a', function(event){
        event.preventDefault(); 
        $('.service-selection').attr('disabled', true);
        $(this).addClass("disabled");
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var page = parseInt($(this).attr('href').split('page=')[1]);
        $('#service-selection-pagination .page-item').addClass('disabled');
        fetch("{{ route('neo.profile.update.services') }}", {
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
                $('.service-selection').attr('disabled', false);
                return response.text();
            }
        })
        .then(data => {
            if (data) {
                $("#service-selection-list").html(data);
            }
        })
        .catch(function(error) {
            console.log(error);
            $('.service-selection').attr('disabled', false);
        })
    });
});
</script>
@endpush