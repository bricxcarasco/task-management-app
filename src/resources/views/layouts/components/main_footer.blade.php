<footer class="footer py-4 pb-5 pb-md-4">
    <div class="container d-md-flex align-items-center justify-content-between py-2">
        <ul class="list-inline fs-sm mb-3 mb-md-0 order-md-2">
            <li class="list-inline-item my-1">
                <a class="nav-link-style js-external-link" href="https://hero.ne.jp/company/" target="_blank">
                    {{ __('Operating Company') }}
                </a>
            </li>
            <li class="list-inline-item my-1">
                <a class="nav-link-style js-external-link" href="https://hero.ne.jp/terms/" target="_blank">
                    {{ __('Terms of Service') }}
                </a>
            </li>
            <li class="list-inline-item my-1">
                <a class="nav-link-style js-external-link" href="https://hero.ne.jp/privacy-policy/" target="_blank">
                    {{ __('Privacy Policy') }}
                </a>
            </li>
        </ul>
        <p class="fs-sm mb-0 me-3 order-md-1">
            <span class="text-muted me-1">Copyright © I'tHERO, Inc. All Rights Reserved.</span>
        </p>
    </div>
</footer>
<div class="btn-toolbar sidebar-toggle btn-toolbar--custom menu__bar" role="toolbar" aria-label="Settings toolbar">
    <div class="btn-group me-2 mb-2" role="group" aria-label="Settings group">
        <a href="{{ route('chat.room.index') }}" class="btn btn-secondary btn-icon">
            <i class="ai-message-square"></i>
            @if ($messagesCount > 0)
                <span class="badge bg-danger">{{ $messagesCount }}</span>
            @endif
        </a>
        <a href="{{ route('schedules.index') }}" class="btn btn-secondary btn-icon">
            <i class="ai-calendar"></i>
            @if ($invitationsCount > 0)
                <span class="badge bg-danger">{{ $invitationsCount }}</span>
            @endif
        </a>
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary btn-icon">
            <i class="ai-list"></i>
        </a>
    </div>
</div>
<a class="btn-scroll-top" href="#top" data-scroll data-fixed-element>
    <span class="btn-scroll-top-tooltip text-muted fs-sm me-2">Top</span>
    <i class="btn-scroll-top-icon ai-arrow-up"> </i>
</a>