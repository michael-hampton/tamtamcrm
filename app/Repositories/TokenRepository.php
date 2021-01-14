<?php

namespace App\Repositories;

use App\Models\CompanyToken;
use App\Repositories\Base\BaseRepository;

class TokenRepository extends BaseRepository
{
    /**
     * TokenRepository constructor.
     * @param CompanyToken $company_token
     */
    public function __construct(CompanyToken $company_token)
    {
        parent::__construct($company_token);
        $this->model = $company_token;
    }

    /**
     * Gets the class name.
     *
     * @return     string The class name.
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param int $id
     * @return CompanyToken
     */
    public function findTokenById(int $id): CompanyToken
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @param array $data
     * @param CompanyToken $company_token
     * @return CompanyToken|null
     */
    public function save(array $data, CompanyToken $company_token): ?CompanyToken
    {
        $company_token->fill($data);

        $company_token->save();

        return $company_token;
    }
}
