<?php

namespace App\Http\Controllers;

use App\Events\Company\CompanyWasCreated;
use App\Factory\CompanyFactory;
use App\Models\Company;
use App\Models\Industry;
use App\Repositories\CompanyContactRepository;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use App\Requests\Company\CreateCompanyRequest;
use App\Requests\Company\UpdateCompanyRequest;
use App\Requests\SearchRequest;
use App\Search\CompanySearch;
use App\Traits\UploadableTrait;
use App\Transformations\CompanyTransformable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{

    use CompanyTransformable, UploadableTrait;

    /**
     * @var CompanyRepositoryInterface
     */
    private $company_repo;

    /**
     * @var CompanyContactRepository
     */
    private $company_contact_repo;

    /**
     * CompanyController constructor.
     * @param CompanyRepositoryInterface $company_repo
     * @param CompanyContactRepository $company_contact_repo
     */
    public function __construct(
        CompanyRepositoryInterface $company_repo,
        CompanyContactRepository $company_contact_repo
    ) {
        $this->company_repo = $company_repo;
        $this->company_contact_repo = $company_contact_repo;
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $brands =
            (new CompanySearch($this->company_repo))->filter($request, auth()->user()->account_user()->account);
        return response()->json($brands);
    }

    /**
     * @param CreateCompanyRequest $request
     * @return JsonResponse
     */
    public function store(CreateCompanyRequest $request)
    {
        $company = (new CompanyFactory)->create(auth()->user(), auth()->user()->account_user()->account);

        if ($request->company_logo !== null && !empty($request->file('company_logo'))) {
            $company->logo = $this->uploadLogo($request->file('company_logo'));
        }

        $company = $this->company_repo->save($request->except('logo'), $company);

        if (!empty($request->contacts)) {
            $this->company_contact_repo->save($request->contacts, $company);
        }

        event(new CompanyWasCreated($company));

        return response()->json($this->transformCompany($company));
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id)
    {
        $brand = $this->company_repo->findCompanyById($id);
        return response()->json($this->transformCompany($brand));
    }

    /**
     * @param UpdateCompanyRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(UpdateCompanyRequest $request, $id)
    {
        $company = $this->company_repo->findCompanyById($id);
        $logo = $company->logo;

        if ($request->company_logo !== null && $request->company_logo !== 'null') {
            $company->logo = $this->uploadLogo($request->file('company_logo'));
        } else {
            if (empty($request->logo) && !empty($logo)) {
                Storage::disk('public')->delete($logo);
                $company->logo = null;
            }
        }

        $this->company_repo->save($request->except('logo'), $company);

        if (!empty($request->contacts)) {
            $this->company_contact_repo->save($request->contacts, $company);
        }

        return response()->json($this->transformCompany($company));
    }


    public function getIndustries()
    {
        $industries = Industry::all();
        return response()->json($industries);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $company = Company::withTrashed()->where('id', '=', $id)->first();
        $company->restoreEntity();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function archive(int $id)
    {
        $company = $this->company_repo->findCompanyById($id);
        $company->archive();
    }

    public function destroy(int $id)
    {
        $company = Company::withTrashed()->where('id', '=', $id)->first();

        $this->authorize('delete', $company);

        $company->deleteEntity();
        return response()->json([], 200);
    }


}
