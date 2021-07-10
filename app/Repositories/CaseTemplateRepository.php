<?php

namespace App\Repositories;

use App\Models\CaseTemplate;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\CaseTemplateRepositoryInterface;

class CaseTemplateRepository extends BaseRepository implements CaseTemplateRepositoryInterface
{
    /**
     * BrandRepository constructor.
     * @param CaseTemplate $template
     */
    public function __construct(CaseTemplate $template)
    {
        parent::__construct($template);
        $this->model = $template;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param array $data
     * @param CaseTemplate $case_template
     * @return CaseTemplate
     */
    public function create(array $data, CaseTemplate $case_template): CaseTemplate
    {
        $case_template->fill($data);
        $case_template->save();
        return $case_template;
    }

    /**
     * @param array $data
     * @param CaseTemplate $case_template
     * @return CaseTemplate
     */
    public function update(array $data, CaseTemplate $case_template): CaseTemplate
    {
        $case_template->update($data);

        return $case_template;
    }

    /**
     * @param int $id
     * @return CaseTemplate
     */
    public function findCaseTemplateById(int $id): CaseTemplate
    {
        return $this->findOneOrFail($id);
    }
}
