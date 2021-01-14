<?php

namespace App\Components\Mail;

use App\Factory\CaseFactory;
use App\Factory\CommentFactory;
use App\Models\Cases;
use App\Models\CustomerContact;
use App\Models\File;
use App\Repositories\CaseRepository;
use BeyondCode\Mailbox\InboundEmail;

class CaseMailHandler
{
    public function __invoke(InboundEmail $email, $token)
    {
        $from = $email->from();

        $contact = CustomerContact::whereEmail($from)->first();

        if (!$contact) {
            return false;
        }

        $email_enabled = $contact->customer->getSetting('case_forwarding_enabled');

        if (!$email_enabled) {
            return true;
        }

        $default_assignee = $contact->customer->getSetting('default_case_assignee');

        $to = $email->to()[0]->getEmail();
        $exploded = explode('@', $to);
        $exploded_hash = explode('+', $exploded[0]);

        if (!empty($exploded_hash[1])) {
            $case = Cases::whereNumber($exploded_hash[1])->first();
            $this->saveComment($email, $case);

            return $case;
        }

        $data = [
            'contact_id'  => $contact->id,
            'customer_id' => $contact->customer->id,
            'subject'     => $email->subject(),
            'message'     => $email->text()
        ];

        if (!empty($default_assignee)) {
            $data['assigned_to'] = $default_assignee;
        }

        $case = CaseFactory::create(auth()->user()->account_user()->account, auth()->user(), $contact->customer);
        $case = (new CaseRepository(new Cases()))->save($data, $case);

        $this->saveComment($email, $case);

        $this->saveAttachments($email, $case);


        return $case;
    }

    private function saveComment(InboundEmail $email, Cases $case)
    {
        $data = [
            'comment'    => $email->text(),
            'user_id'    => auth()->user()->id,
            'account_id' => auth()->user()->account_user()->account->id
        ];

        $comment = CommentFactory::create(auth()->user()->id, auth()->user()->account_user()->account->id);
        $comment->fill($data);
        $case->comments()->save($comment);

        return true;
    }

    private function saveAttachments(InboundEmail $email, Cases $case)
    {
        if (empty($email->attachments())) {
            return true;
        }

        foreach ($email->attachments() as $attachment) {
            $filename = $attachment->getFilename();

            $attachment->saveContent(public_path('files/' . $filename));

            $file = new File;
            $file->user_id = auth()->user()->id;
            $file->account_id = auth()->user()->account_user()->account->id;
            $file->file_path = public_path('files/' . $filename);
            $file->name = $filename;
            $file->type = pathinfo($filename, PATHINFO_EXTENSION);
            $file->uploaded_by_customer = false;
            $file->customer_can_view = true;

            $case->files()->save($file);
        }

        return true;
    }
}
