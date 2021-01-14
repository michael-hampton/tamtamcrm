<?php

namespace App\Http\Controllers;

use App\Factory\CaseTemplateFactory;
use App\Repositories\CaseTemplateRepository;
use App\Requests\CaseTemplate\CreateCaseTemplateRequest;
use App\Requests\CaseTemplate\UpdateCaseTemplateRequest;
use App\Requests\SearchRequest;
use App\Search\CaseTemplateSearch;
use App\Transformations\CaseTemplateTransformable;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Class BrandController
 * @package App\Http\Controllers
 */
class CaseTemplateController extends Controller
{
    use CaseTemplateTransformable;

    /**
     * @var CaseTemplateRepository
     */
    private CaseTemplateRepository $template_repo;

    /**
     * CaseTemplateController constructor.
     * @param CaseTemplateRepository $case_template_repo
     */
    public function __construct(CaseTemplateRepository $case_template_repo)
    {
        $this->template_repo = $case_template_repo;
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $templates = (new CaseTemplateSearch($this->template_repo))->filter(
            $request,
            auth()->user()->account_user()->account
        );
        return response()->json($templates);
    }

    /**
     * @param CreateCaseTemplateRequest $request
     * @return JsonResponse
     */
    public function store(CreateCaseTemplateRequest $request)
    {
        $template = $this->template_repo->save(
            $request->all(),
            CaseTemplateFactory::create(auth()->user()->account_user()->account, auth()->user())
        );
        return response()->json($this->transformCaseTemplate($template));
    }

    /**
     * @param UpdateCaseTemplateRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(UpdateCaseTemplateRequest $request, $id)
    {
        $template = $this->template_repo->findCaseTemplateById($id);
        $template = $this->template_repo->save($request->all(), $template);

        return response()->json($this->transformCaseTemplate($template));
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id)
    {
        $template = $this->template_repo->findCaseTemplateById($id);
        $template->deleteEntity();

        return response()->json('deleted');
    }
}
