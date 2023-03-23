<div class="d-flex align-items-center card-horizontal">
    <img class="rounded-circle img--profile_image_md" src="{{ asset($rio->rio_profile->profile_image) }}" onerror="this.src='{{ asset('images/profile/user_default.png') }}'" alt="Product" width="80">
    <p class="m-0 ms-4" style="flex: 1;">
        {{ $rio->full_name }}
    </p>
    @if($isProfileOwner)
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#privacy">
            <i class="ai-more-vertical"></i>
        </button>
    @else
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
        </x-function-info-button>
    @endif
</div>
