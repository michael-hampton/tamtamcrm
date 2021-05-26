<?php

namespace App\Repositories;

use App\Models\Email;
use App\Models\EmailTemplate;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Collection;

class EmailTemplateRepository extends BaseRepository
{

    /**
     * EmailRepository constructor.
     * @param Email $email
     */
    public function __construct(EmailTemplate $email_template)
    {
        parent::__construct($email_template);
        $this->model = $email_template;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param int $id
     *
     * @return Email
     */
    public function findEmailById(int $id): Email
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @param array $columns
     * @param string $orderBy
     * @param string $sortBy
     *
     * @return Collection
     */
    public function listEmails($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Collection
    {
        return $this->all($columns, $orderBy, $sortBy);
    }

    public function getTemplateForType($template_type)
    {
        return EmailTemplate::query()->where('template', $template_type)->first();
    }


    /**
     * @param array $data
     * @param Email $email
     * @return Email|null
     */
    public function save(array $data, EmailTemplate $email_template): ?EmailTemplate
    {
        $email_template->fill($data);
        $email_template->save();


        return $email_template->fresh();
    }
}
