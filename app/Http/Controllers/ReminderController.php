<?php


namespace App\Http\Controllers;


use App\Models\Bank;
use App\Models\Reminders;
use App\Requests\Account\StoreReminders;
use App\Transformations\RemindersTransformable;

class ReminderController
{
    use RemindersTransformable;

    public function index()
    {
        $account_id = auth()->user()->account_user()->account->id;

        $list = Reminders::where('account_id', $account_id)->get();
        $reminders = $list->map(
            function (Reminders $reminder) {
                return $this->transformReminders($reminder);
            }
        )->all();

        return response()->json($reminders);
    }

    public function store(StoreReminders $request)
    {
        $account_id = auth()->user()->account_user()->account->id;

        Reminders::query()->where('account_id', $account_id)->delete();

        foreach ($request->input('reminders') as $reminder) {
            $reminder['account_id'] = $account_id;
            $reminder['user_id'] = auth()->user()->id;

            Reminders::create($reminder);
        }

        $list = Reminders::where('account_id', $account_id)->get();
        $reminders = $list->map(
            function (Reminders $reminder) {
                return $this->transformReminders($reminder);
            }
        )->all();

        return response()->json($reminders);
    }
}