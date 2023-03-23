<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

abstract class EmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get Placeholder Variables Method
     *
     * Gets placeholder variables on the given body
     *
     * Example. My name is {{name}} and I am {{age}} years old.
     * This would extract and return an array:
     * [0 => 'name', 1 => 'age']
     *
     * @param string $body - string to extract thevariables from
     * @return array - contains array of variables extracted
     */
    protected function getPlaceholderVariables($body)
    {
        preg_match_all('/\{\{(.*?)\}\}/', $body, $matches);

        return array_map('trim', $matches[1]);
    }

    /**
     * Parse Mail Subject using variables.
     *
     * Replaces the variables inside the subject using the content value.
     *
     * Example.
     * Body : My name is {{name}} and I am {{age}} years old.
     * Content : ['name' => 'Jimmy', 'age' => '22']
     *
     * Result : My name is Jimmy and I am 22 years old.
     *
     * @param string $subject - string to extract thevariables from
     * @param array $variables - array of content to be placed
     * @return string
     */
    protected function parseMailSubjectWithVariables($subject, $variables = [])
    {
        $subjectVariables = $this->getPlaceholderVariables($subject);

        foreach ($subjectVariables as $index) {
            if (isset($variables[$index])) {
                $subject = str_replace('{{' . $index . '}}', $variables[$index], $subject);
            }
        }

        return $subject;
    }

    /**
     * Parse Mail Body with Content Method
     *
     * Replaces the variables inside the body using the content value.
     *
     * Example.
     * Body : My name is {{name}} and I am {{age}} years old.
     * Content : ['name' => 'Jimmy', 'age' => '22']
     *
     * Result : My name is Jimmy and I am 22 years old.
     *
     * @param string $body - string to extract thevariables from
     * @param array $content - array of content to be placed
     * @return string
     */
    protected function parseMailBodyWithContent($body, $content = [])
    {
        $contentVariables = $this->getPlaceholderVariables($body);

        foreach ($contentVariables as $index) {
            if (isset($content[$index])) {
                $body = str_replace('{{' . $index . '}}', $content[$index], $body);
            }
        }

        return $body;
    }
}
