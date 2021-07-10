<?php

namespace App\Services\Cases;


use App\Services\Email\DispatchEmail;
use App\Models\Cases;
use App\Models\CaseTemplate;
use App\Traits\BuildVariables;
use Carbon\Carbon;

class TriggerEmail
{
    use BuildVariables;

    private Cases $case;

    public function __construct(Cases $case)
    {
        $this->case = $case;
    }

    public function triggerEmail($status = '')
    {
        if (!empty($status)) {
            $this->sendEmail($status);
            return true;
        }

        if ($this->case->status_id === Cases::STATUS_OPEN && empty($this->case->date_opened)) {
            $this->case->date_opened = Carbon::now();
            $this->case->opened_by = !empty(auth()->user()->id) ? auth()->user()->id : $this->case->user_id;
            $this->case->save();

            $this->sendEmail('open');
        }

        if ($this->case->status_id === Cases::STATUS_CLOSED && empty($this->case->date_closed)) {
            $this->case->date_closed = Carbon::now();
            $this->case->closed_by = auth()->user()->id;
            $this->case->save();

            $this->sendEmail('closed');
        }
    }

    /**
     * @param Cases $case
     * @param string $status
     * @return bool
     */
    private function sendEmail(string $status)
    {
        $template_id = $this->case->customer->getSetting('case_template_' . $status);

        $template = CaseTemplate::where('id', '=', $template_id)->first();

        if (!empty($template)) {
            (new DispatchEmail($this->case))->execute(
                null,
                $template->name,
                $this->parseCaseVariables($template->description, $this->case)
            );
        }

        return true;
    }
}