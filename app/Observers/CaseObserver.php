<?php

namespace App\Observers;

use App\Actions\Cases\TriggerEmail;
use App\Factory\CommentFactory;
use App\Models\Cases;
use App\Traits\BuildVariables;

class CaseObserver
{
    use BuildVariables;

    /**
     * Handle the Cases "created" event.
     *
     * @param \App\Models\Cases $cases
     * @return void
     */
    public function created(Cases $case)
    {
        $comment = CommentFactory::create($case->user_id, $case->account_id);
        $comment->comment = $case->message;
        $case->comments()->save($comment);

        (new TriggerEmail($case))->triggerEmail('new');
    }

    /**
     * Handle the Cases "updated" event.
     *
     * @param \App\Models\Cases $cases
     * @return void
     */
    public function updated(Cases $case)
    {
        (new TriggerEmail($case))->triggerEmail();
    }

    /**
     * Handle the Cases "deleted" event.
     *
     * @param \App\Models\Cases $cases
     * @return void
     */
    public function deleted(Cases $cases)
    {
        //
    }

    /**
     * Handle the Cases "restored" event.
     *
     * @param \App\Models\Cases $cases
     * @return void
     */
    public function restored(Cases $cases)
    {
        //
    }

    /**
     * Handle the Cases "force deleted" event.
     *
     * @param \App\Models\Cases $cases
     * @return void
     */
    public function forceDeleted(Cases $cases)
    {
        //
    }

    public function saving(Cases $case)
    {
        $case->message = $this->parseCaseVariables($case->message, $case);
    }
}
