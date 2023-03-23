@php
    use \App\Enums\Neo\NeoBelongStatusType;
@endphp

<div class="tab-content" id="invitation-lists">
    @csrf
    <div class="tab-pane fade show active" id="application" role="tabpanel">
        <ul class="list-group list-group-flush mt-4">
        <div class="d-flex justify-content-between mb-2">
            <span>
                {{ __('Inviting RIO') }}
            </span>

            {{-- NEO Invitation Management Directions --}}
            <x-function-info-button>
                <h5 class="border-bottom">RIOを参加招待</h5>
                <p class="fs-sm mb-4">NEOが参加させたいつながりのあるRIOのみを招待することができます。</p>
                <h6 class="border-bottom">使い方</h6>
                <p class="fs-xs">
                    1. ホーム画面右上の「→」アイコンをタップし選択中のNEOのプロフィールページに移動します。
                    <br>
                    2. 画面右上の「...」アイコンをタップします。
                    <br>
                    3. 管理メニューのモーダルが表示され、メニューリスト下の「参加申請・招待管理」をタップします。
                    <br>
                    4. 「つながりのRIOを招待」をタップします。
                    <br>
                    5. つながりRIO 一覧の中から対象の「RIO」探し、「招待」をタップします。
                </p>
            </x-function-info-button>
        </div>
        @foreach ($connectedLists as $connected)
            <li class="list-group-item px-0 py-2 position-relative list--white px-2">
                <a href="{{ route('rio.profile.introduction', $connected->rio_id)}}">
                    <img class="rounded-circle me-2 d-inline-block img--profile_image_sm" src="{{ $connected->profile_photo }}" alt="user-img" width="40">   
                </a>  
                <a href="{{ route('rio.profile.introduction', $connected->rio_id)}}">
                    <span class="fs-xs c-primary ms-2">{{ $connected->name }}</span>
                </a>  
                <div class="vertical-right">
                    <button class="fs-xs btn btn-link" id="cancel-invitation" data-id="{{ $connected->rio_id }}" data-user-name="{{ $connected->name }}">{{ __('Cancel invitation') }}</button>
                </div>
            </li>
        @endforeach
        </ul>
    </div>
    <div class="d-flex justify-content-center mt-4" id="invitation-pagination">
        {!! $connectedLists->links() !!}
    </div> 
</div>