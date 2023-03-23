@php
    use App\Enums\Neo\RoleType;
@endphp
<div class="modal fade" id="privacy" tabindex="-1" style="display: none;" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title ">{{ __('Management Menu') }}</h4>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <a href="{{ route('neo.profile.edit', ['neo' => $neo->id]) }}" class="d-block c-primary">{{ __('Profile Settings') }}</a>
                    </li>
                    <li class="list-group-item">
                        <a href="{{ route('neo.privacy.edit', ['neo' => $neo->id]) }}" class="d-block c-primary">{{ __('Privacy Settings') }}</a>
                    </li>
                    @if((($neoBelong->role ?? null) === RoleType::OWNER))
                        <li class="list-group-item">
                            <a href="{{ route('neo.administrator.index', ['neo' => $neo->id ]) }}" class="d-block c-primary">
                                {{ __('Administrator management') }}
                            </a>
                        </li>
                    @endif
                    @if((($isOwnerOrAdmin->role ?? null) !== RoleType::ADMINISTRATOR))
                        <li class="list-group-item d-flex justify-content-between">
                            <a href="{{ route('neo.profile.participants', ['neo' => $neo->id ]) }}" class="d-block c-primary w-100">{{ __('Participation application/invitation management') }}</a>

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
                        </li>
                    @endif
                    <li class="list-group-item d-flex align-items-center">
                        <a href="{{ route('plans.subscription') }}" class="d-block c-primary">{{ __('Confirmation and change of contract information') }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>