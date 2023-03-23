<div class="btn-group" role="group">
    <button type="button" class="btn btn-link px-3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="color-primary ai-more-vertical"></i>
    </button>
    <div class="dropdown-menu">
        <button class="dropdown-item js-update-group-btn" data-name="{{ $group->group_name }}"
            data-action="{{ route('neo.profile.group.edit', ['group' => $group->id]) }}">
            {{ __('Update group name') }}
        </button>
        <button class="dropdown-item js-member-list-btn"
            data-action="{{ route('neo.profile.group.members-list', ['group' => $group->id]) }}">
            {{ __('Group member list') }}
        </button>
        <button class="dropdown-item js-add-member-btn"
            data-action="{{ route('neo.profile.group.participant-user-list', ['group' => $group->id]) }}">
            {{ __('Add group member') }}
        </button>
        <button class="dropdown-item js-delete-group-btn"
            data-action="{{ route('neo.profile.group.delete', ['group' => $group->id]) }}">
            {{ __('Delete group') }}
        </button>
    </div>
</div>
<div class="clearfix"></div>
