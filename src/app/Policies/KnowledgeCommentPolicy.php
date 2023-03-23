<?php

namespace App\Policies;

use App\Models\KnowledgeComment;
use App\Traits\HandlesAuthorization;

class KnowledgeCommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can edit/delete comment
     *
     * @param   \App\Models\User $user
     * @param   \App\Models\KnowledgeComment $knowledgeComment
     * @return  \Illuminate\Auth\Access\Response
     */
    public function modifiable($user, KnowledgeComment $knowledgeComment)
    {
        // Guard clause for non-existing rio
        if (empty($user->rio)) {
            return $this->deny();
        }

        return $knowledgeComment->rio_id === $user->rio->id
            ? $this->allow()
            : $this->deny();
    }
}
