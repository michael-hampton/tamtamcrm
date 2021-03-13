<?php

namespace App\Repositories\Interfaces;

use App\Models\CaseTemplate;
use App\Repositories\Base\BaseRepositoryInterface;

interface CaseTemplateRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param array $data
     * @param CaseTemplate $case_template
     * @return CaseTemplate
     */
    public function create(array $data, CaseTemplate $case_template): CaseTemplate;

    /**
     * @param array $data
     * @param CaseTemplate $case_template
     * @return CaseTemplate
     */
    public function update(array $data, CaseTemplate $case_template): CaseTemplate;

    /**
     * @param int $id
     * @return CaseTemplate
     */
    public function findCaseTemplateById(int $id): CaseTemplate;


}
