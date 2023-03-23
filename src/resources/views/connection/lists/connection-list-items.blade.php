<div id="connection-list-items">
    <div class="card p-2 px-4 shadow mt-2" >
        @csrf

        {{-- Section loader --}}
        @include('components.section-loader', ['show' => false])
        
        <div class="connection__wrapper">
            <ul class="connection__lists list-group list-group-flush mt-2">
                @foreach ($result as $results)
                    <li class="list-group-item px-0 py-2 position-relative list--white px-2">
                        <img class="rounded-circle me-2 d-inline-block img--profile_image_sm" src="{{ asset($results->profile_photo) }}" onerror="this.src='{{ asset('images/profile/user_default.png') }}'" alt="Product" width="40">
                        <span class="fs-xs c-primary ms-2">{{ $results->name }}</span>
                        <div class="vertical-right d-flex align-items-center justify-content-center">
                            @if($results->service === 'RIO')
                                <a href="{{ route('rio.profile.introduction', $results->id) }}"><i class="ai-arrow-right"></i></a>
                            @elseif($results->service === 'NEO')
                                <a href="{{ route('neo.profile.introduction', $results->id) }}"><i class="ai-arrow-right"></i></a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        @if($result->total() === 0)
        <div class="d-flex justify-content-center mt-3">
            {{ __('There is no connection') }} 
        </div>
        @endif

        <div class="d-flex justify-content-center mt-1">
            {!! $result->links() !!}
        </div>
    </div>
</div>