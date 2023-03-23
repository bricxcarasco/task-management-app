@extends('layouts.main')

@section('content')
    <div class="container position-relative zindex-5 pt-6 py-md-6 home--height">
        <div class="row">
            <div class="col-12 offset-md-3 col-md-9">
                <div class="d-flex align-items-center justify-content-between  mb-0 mb-md-2">
                    <h3>{{ __('Notification list') }}</h3>
                </div>
                <div class="btn-group w-25">
                    <a href="{{ route('notifications.index') }}"
                        class="btn btn-outline-secondary {{ $activeTab == 'index' ? 'btn--active' : '' }}">
                        {{ __('All') }}
                    </a>
                    <a href="{{ route('notifications.unread') }}"
                        class="btn btn-outline-secondary {{ $activeTab == 'unread' ? 'btn--active' : '' }}">
                        {{ __('Unread') }}
                    </a>
                </div>
                <div class="d-flex flex-column rounded-3 bg-light shadow-lg mt-4">
                    @if ($notifications->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-bordered table--notification m-0">
                                @foreach ($notifications as $notification)
                                    <tr data-url="{{ route('notifications.read', $notification->id) }}"
                                        class="js-read-notif hoverable {{ $notification->is_notification_read ? '' : 'unread' }}">
                                        <td class="text-muted align-middle text-center fs-md">
                                            {{ $notification->notification_date }}
                                        </td>
                                        <td>
                                            <span class="text-muted fs-xs d-block">
                                                {{ $notification->notification_recipient }}
                                            </span>
                                            {{ $notification->notification_content }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @else
                        <p class="text-center my-5">
                            {{ __('No notifications') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('js/notifications.js') }}" defer></script>
@endpush
