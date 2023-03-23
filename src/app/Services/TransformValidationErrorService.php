<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;

class TransformValidationErrorService
{
    /**
     * Validator Message Bag
     *
     * @var \Illuminate\Support\MessageBag
     */
    private $messageBag;

    /**
     * Constructor function
     *
     * @param \Illuminate\Support\MessageBag $messageBag
     * @return void
     */
    public function __construct(MessageBag $messageBag)
    {
        $this->messageBag = $messageBag;
    }

    /**
     * Format validation errors
     *
     * @return array
     */
    public function formatErrors()
    {
        // Get messages and instantiate a collection
        $messages = $this->messageBag->getMessages();
        $messages = new Collection($messages);

        // Format messages
        return $messages
            ->map(function ($message, $field) {
                return [
                    'field_name' => $field,
                    'message' => $message[0],
                ];
            })
            ->values()
            ->toArray();
    }
}
