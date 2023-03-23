@php
    use App\Enums\Connection\ListFilters;
@endphp

<connection-application-request
    :image_path='@json(asset(config("bphero.profile_image_directory") . config("bphero.profile_image_filename")))'
    :status='@json($status)'
    :mode='@json($mode)'>
    <template v-slot:header>
        <form id='form' action="{{ route('connection.application-list') }}" method="get">
            {{ csrf_field() }}
            <div class="form-check">
                <select class="form-select form-select-sm w-100" name='mode' id="mode" >
                    @foreach (ListFilters::asSelectArray() as $value => $text)
                        <option value="{{ $value }}">{{ $text }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </template>
</connection-application-request>