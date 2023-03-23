<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class ValidTimeRangeRule implements Rule
{
    /** @var object */
    private $request;

    /**
     * Create a new rule instance.
     *
     * @param \Illuminate\Foundation\Http\FormRequest $request
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Parse dates
        $startDate = Carbon::parse($this->request->start_date);
        $endDate = Carbon::parse($this->request->end_date);
        $now = Carbon::now();

        if ($startDate < $now || $endDate < $now) {
            // Check if valid time for past dates
            return $this->request->start_time < $this->request->end_time;
        }

        if ($startDate < $endDate) {
            // Check if valid date time range for multiple day event
            $startDateTime = $this->request->start_date . ' ' . $this->request->start_time;
            $endDateTime = $this->request->end_date . ' ' . $this->request->end_time;
            $startDateTimeObject = Carbon::parse($startDateTime);
            $endDateTimeObject = Carbon::parse($endDateTime);

            return ($startDateTimeObject < $endDateTimeObject);
        } else {
            // Check if valid time for same day
            if ($this->request->start_time >= $this->request->end_time) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        /** @var string */
        return __('validation.custom.end_time.date_format');
    }
}
