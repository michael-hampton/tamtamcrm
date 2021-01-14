<?php

namespace App\Transformations;


use App\Models\CompanyToken;

trait TokenTransformable
{

    /**
     * @param CompanyToken $company_token
     * @return array
     */
    public function transform(CompanyToken $company_token)
    {
        return [
            'id'          => (int)$company_token->id,
            'user_id'     => (int)$company_token->user_id,
            'name'        => $company_token->name ?: '',
            'token'       => $company_token->token ?: '',
            'updated_at'  => $company_token->updated_at,
            'archived_at' => $company_token->deleted_at,
            'created_at'  => $company_token->created_at,
            'is_deleted'  => (bool)$company_token->is_deleted,
        ];
    }
}
