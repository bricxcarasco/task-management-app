@extends('layouts.main')

@section('content')
    <div class="container position-relative zindex-5 pt-6 py-md-6 mb-md-3 home--height">
        <div class="row">
            <div class="col-12 col-md-9 offset-md-3">
                <div class="d-flex align-items-center justify-content-center mb-0 mb-md-2 position-relative border-bottom">
                    <a href="{{ route('schedules.index') }}" class="btn btn-secondary btn--back">
                        <i class="ai-arrow-left"></i>
                    </a>
                    <h3>{{ __('Invitation notification') }}</h3>
                </div>
                <div class="mt-2">
                    <ul class="list-group fs-sm mb-3 mb-md-0">
                        @if ($notifications->isNotEmpty())
                            @foreach ($notifications as $notification)
                                <a href="{{ route('schedules.show', $notification->id) }}" class="list-group-item p-3">
                                    <li class="d-block">
                                        <p class="m-0">
                                            {{ __('Host') }}：{{ $notification->host_name }}
                                        </p>
                                        <p class="m-0">
                                            {{ $notification->schedule_title }}
                                        </p>
                                        <br>
                                        <p class="m-0">
                                            {{ __('Start') }}：{{ $notification->start_datetime }}
                                        </p>
                                        <p class="m-0">
                                            {{ __('End') }}：{{ $notification->end_datetime }}
                                        </p>
                                    </li>
                                </a>
                            @endforeach
                        @else
                            <p class="text-center my-5">
                                {{ __('No invitation notifications') }}
                            </p>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
