<?php

namespace App\Policies;

use App\Objects\TalkSubject;
use App\Traits\HandlesAuthorization;

class ChatPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the group connection.
     *
     * @return  \Illuminate\Auth\Access\Response
     */
    public function list()
    {
        // Get list and current subject
        $subject = TalkSubject::getSelected();
        $subjectList = collect(TalkSubject::getList());

        // Check if session is in subject list
        $isValidSession = $subjectList->contains(function ($entity) use ($subject) {
            return $subject->type === $entity['type']
                && $subject->data->id === $entity['id'];
        });

        return ($isValidSession)
            ? $this->allow()
            : $this->deny();
    }
}
