<?php


namespace App\Traits\Notifications;

trait UserNotifies
{
    public function findUserNotificationTypesByInvitation(
        $invitation,
        $account_user,
        $entity_name,
        $required_permissions
    ) {
        if ($invitation->inviteable->user_id == $account_user->_user_id ||
            $invitation->inviteable->assigned_to == $account_user->user_id) {
            array_push($required_permissions, "all_user_notifications");
        }

        $notifiable_methods = $this->getNotificationTypesForAccountUser($account_user, $required_permissions);

        return $notifiable_methods;
    }

    public function getNotificationTypesForAccountUser($account_user, $required_permissions): array
    {
        $notifiable_methods = [];

        $notifications = json_decode(json_encode($account_user->notifications->email), true);

        if (empty($notifications)) {
            return $notifiable_methods;
        }

        $found = array_filter(
            $notifications,
            function ($v, $k) {
                return $v['isChecked'] == 1;
            },
            ARRAY_FILTER_USE_BOTH
        );

        $keys = array_column($found, 'value');

        if (count(array_intersect($required_permissions, $keys)) >= 1) {
            array_push($notifiable_methods, 'mail');
        }

        return $notifiable_methods;
    }

}
