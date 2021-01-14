<?php

namespace App\Services\Cases;

use App\Components\Pdf\TaskPdf;
use App\Factory\CommentFactory;
use App\Jobs\Pdf\CreatePdf;
use App\Models\Cases;
use App\Models\User;
use App\Services\ServiceBase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use ReflectionException;

/**
 * Class TaskService
 * @package App\Services\Task
 */
class CasesService extends ServiceBase
{
    /**
     * @var Cases
     */
    protected Cases $case;

    /**
     * CasesService constructor.
     * @param Cases $case
     */
    public function __construct(Cases $case)
    {
        $config = [
            'email'   => $case->account->getSetting('should_email_lead'),
            'archive' => $case->account->getSetting('should_archive_lead')
        ];

        parent::__construct($case);
        $this->case = $case;
    }

    /**
     * @param null $contact
     * @param string $subject
     * @param string $body
     * @param string $template
     * @return bool
     */
    public function sendEmail($contact = null, $subject = '', $body = '', $template = 'case')
    {
        return (new CaseEmail($this->case, $subject, $body))->execute();
    }

    /**
     * @param null $contact
     * @param bool $update
     * @return mixed|string
     * @throws ReflectionException
     */
    public function generatePdf($contact = null, $update = false)
    {
        if (!$contact) {
            $contact = $this->case->customer->primary_contact()->first();
        }

        return CreatePdf::dispatchNow((new TaskPdf($this->case)), $this->case, $contact, $update, 'case');
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Cases|null
     */
    public function mergeCase(Request $request, User $user): ?Cases
    {
        $this->case->merged_case_id = $request->input('parent_id');
        $this->case->date_closed = Carbon::now();
        $this->case->closed_by = $user->id;
        $this->case->status_id = Cases::STATUS_MERGED;
        $this->case->save();

        $comment = CommentFactory::create($user->id, $this->case->account_id);
        $comment->comment = 'Case has been merged';
        $this->case->comments()->save($comment);

        $new_case = Cases::where('id', '=', $request->input('parent_id'))->first();
        $comment = CommentFactory::create($user->id, $new_case->account_id);
        $comment->comment = 'A case has been merged';
        $new_case->comments()->save($comment);

        $new_case->has_merged_case = true;
        $new_case->save();

        return $new_case;
    }

}
