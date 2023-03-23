@php
use App\Enums\Neo\NeoBelongStatusType;
use App\Enums\Neo\RoleType;
use App\Enums\ServiceSelectionTypes;
@endphp

<div class="d-flex align-items-center card-horizontal">
    <img class="rounded-circle img--profile_image_md" src="{{ asset($neo->neo_profile->profile_image) }}" alt="{{ $neo->organization_name }}" onerror="this.src='{{ asset('images/profile/user_default.png') }}'" width="80">
    <p class="m-0 ms-4" style="flex:1;">{{ $neo->organization_name }}</p>
    @if((($serviceSelected->type === ServiceSelectionTypes::NEO && $serviceSelected->data->id === $neo->id) &&
        (in_array(($neoBelong->role ?? null), [RoleType::OWNER]))) ||
            (in_array(($neoBelong->role ?? null), [RoleType::OWNER, RoleType::ADMINISTRATOR]) &&
            (($neoBelong->status ?? null) === NeoBelongStatusType::AFFILIATE)))
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#privacy">
            <i class="ai-more-vertical"></i>
        </button>
    @else
        {{-- Connection Application Directions --}}
        <x-function-info-button class="position-absolute p-2" style="top: 0; right: 0;">
            <h5 class="border-bottom">つながり申請</h5>
            <p class="fs-sm mb-4">
                「つながり申請」には「NEOつながり申請」と「RIOつながり申請」の2つがあります。
                <br>
                つながりになることで直接チャット、グループチャット参加、NEOチームチャットの参加やスケジュール共有、ドキュメント共有が可能になります。
            </p>
            <h6 class="border-bottom">使い方</h6>
            <p class="fs-xs">
                1. ホーム画面のダッシュボードにて「つながり」をタップします。
                <br>
                2. 画面上部にあるメニューの中から「専門家検索」をタップします。
                <br>
                3. 検索対象 の「NEO」と「RIO」から選択します。
                <br>
                4. 該当する「業種」、「経験年数」、「フリーワード」があれば記入し「検索」をタップします。
                <br>
                5. 結果一覧の中から対象の「NEO」または「RIO」を探してタップします。
                <br>
                6. 相手のページに移動したら、プロフィール写真の下にある［つながり申請］をタップします。
            </p>

            <hr class="border border-primary my-4">

            <h5 class="border-bottom">NEOへ参加申請</h5>
            <p class="fs-sm mb-4">
                RIOが参加したいつながり有無にかかわらずNEOに参加申請することができます。
            </p>
            <h6 class="border-bottom">使い方</h6>
            <p class="fs-xs mb-4">
                1. ホーム画面のダッシュボードにて「つながり」をタップします。
                <br>
                2. 画面上部にあるメニューの中から「専門家検索」をタップします。
                <br>
                3. 検索対象 の「NEO」を選択します。
                <br>
                4. 該当する「業種」、「経験年数」、「フリーワード」があれば記入し「検索」をタップします。
                <br>
                5. 結果一覧の中から対象の「NEO」を探してタップします。
                <br>
                6. 相手のページに移動したら、プロフィール写真の下にある［参加申請］をタップします。
            </p>

            <h5 class="border-bottom">NEOグループ参加</h5>
            <p class="fs-sm mb-4">
                つながり有無にかかわらず参加しているNEOが作成したグループに参加することができます。
            </p>
            <h6 class="border-bottom">使い方</h6>
            <h6>参加中のNEOの画面に移動する</h6>
            <p class="fs-xs mb-4">
                1. ホーム画面右上のアカウントアイコンをタップし、参加中のNEOを選択します。
                <br>
                2. NEOホーム画面の「→」アイコンをタップします。
            </p>
            <h6>NEOが作成したグループに参加する</h6>
            <p class="fs-xs">
                3. NEOプロフィール画面の「参加者」をタップします。
                <br>
                4.さらに「グループ一覧」をタップします。
                <br>
                5. 対象のNEOグループを探して「参加する」をタップします。
                <br>
                6. 「このグループに参加しますか？」のモーダルが表示され、「参加する」をタップします。
            </p>
        </x-function-info-button>
    @endif
</div>