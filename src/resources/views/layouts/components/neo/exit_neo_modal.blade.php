<div class="modal fade" id="exit-modal" tabindex="-1" style="display: none;" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Section Loader --}}
            @include('components.section-loader', ['show' => false])

            <div class="modal-header">
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('neo.profile.exit-neo', $neoBelong->id ?? 0) }}" id="exitForm">
                    {{ csrf_field() }}
                    <p class="text-center">{{ $neo->organization_name }}<br/>
                        {{ __('Do you wish to exit from the') }}
                    </p>
                    <div class="d-flex align-items-center justify-content-center">
                        <input class="btn btn-primary btn--dialogue" id="confirmExitBTN" type="submit" style="width: 200px;" value="{{ __('Im leaving.') }}"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            const loader = $('.js-section-loader');

            $('#exitForm').submit(function() {
                // Start loader
                loader.removeClass('d-none');
                return true;
            });
        });

    </script>
@endpush