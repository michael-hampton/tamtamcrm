<?php

namespace App\Repositories;

use App\Factory\CompanyContactFactory;
use App\Models\Company;
use App\Models\CompanyContact;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\Hash;

/**
 * VendorContactRepository
 */
class CompanyContactRepository extends BaseRepository
{

    /**
     * CompanyContactRepository constructor.
     * @param CompanyContact $contact
     */
    public function __construct(CompanyContact $contact)
    {
        parent::__construct($contact);
        $this->model = $contact;
    }

    public function save(array $contacts, Company $company): bool
    {
        $old_ids = CompanyContact::whereCompanyId($company->id)->pluck('id')->toArray();


        $updates = collect(
            array_filter(
                $contacts,
                function ($item) {
                    if (isset($item['id'])) {
                        return true;
                    }
                    return false;
                }
            )
        )->keyBy('id')->toArray();

        $insert = array_filter(
            $contacts,
            function ($item) {
                if (!isset($item['id'])) {
                    return true;
                }
                return false;
            }
        );

        $new_ids = array_keys($updates);

        $update = array_intersect_key($new_ids, $old_ids);
        //^^^Returns all records of $new_ids that were present in $old_ids
        $delete = array_diff_key($old_ids, $new_ids);
        //^^^Returns all records of $old_ids that aren't present in $new_ids


        /******************************* Delete *********************************/
        if (!empty($delete)) {
            ClientContact::destroy($delete);
        }

        /********************************Update ************************************/
        if (!empty($update)) {
            $contacts = CompanyContact::whereIn('id', $update)->get()->keyBy('id');

            foreach ($updates as $key => $update) {
                if (!isset($contacts[$key])) {
                    continue;
                }

                $contact = $contacts[$key];
                $contact->fill($update);
                $contact->password = isset($update['password']) && strlen($update['password']) > 0 ? Hash::make(
                    $update['password']
                ) : $contact->password;
                $contact->save();
            }
        }

        /*******************************Create****************************************/
        if (!empty($insert)) {
            foreach ($insert as $item) {
                $create_contact = CompanyContactFactory::create($company->account_id, $company->user_id);
                $create_contact->company_id = $company->id;
                $create_contact->fill($item);
                $create_contact->password = isset($item['password']) && strlen($item['password']) > 0 ? Hash::make(
                    $item['password']
                ) : '';
                $create_contact->save();
            }
        }

        return true;
    }
}
