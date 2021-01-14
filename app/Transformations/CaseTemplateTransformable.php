<?php

namespace App\Transformations;

use App\Models\CaseTemplate;

trait CaseTemplateTransformable
{

    /**
     * @param CaseTemplate $template
     * @return array
     */
    protected function transformCaseTemplate(CaseTemplate $template)
    {
        return [
            'id'          => (int)$template->id,
            'name'        => $template->name,
            'send_on'     => $template->send_on,
            'description' => $template->description,
            'account_id'  => $template->account_id,
        ];
    }

}
