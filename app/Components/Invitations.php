<?php


namespace App\Components;


use App\Factory\InvitationFactory;

class Invitations
{
    public function generateInvitations($entity, $data)
    {
        if (empty($data['invitations'])) {
            return true;
        }

        $old_values = $entity->invitations->pluck('contact_id')->toArray();
        $new_values = array_column(collect($data['invitations'])->toArray(), 'contact_id');

        if ($deleted = Arrays::keysDeleted($new_values, $old_values)) {
            $entity->invitations()->whereIn('contact_id', $deleted)->forceDelete();
        }

        if ($created = Arrays::keysCreated($new_values, $old_values)) {
            $this->createNewInvitation($created, $entity);
        }

        return true;
    }

    public function createNewInvitation($created, $entity)
    {
        foreach ($created as $contact_id) {
            /* $invitation = $invitation__class::where($id_key, $entity->id)->where('contact_id', $contact_id)->first();

            if($invitation) {
                continue;
            } */

            $new_invitation = InvitationFactory::create($entity->account, $entity->user);
            $new_invitation->contact_id = $contact_id;
            $entity->invitations()->save($new_invitation);
        }

        return true;
    }
}