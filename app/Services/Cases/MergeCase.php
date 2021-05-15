<?php

namespace App\Services\Cases;


use App\Factory\CommentFactory;
use App\Models\Cases;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MergeCase
{

    private Cases $case;

    public function __construct(Cases $case)
    {
        $this->case = $case;
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Cases|null
     */
    public function execute(Request $request, User $user): ?Cases
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