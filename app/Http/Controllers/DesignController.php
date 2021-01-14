<?php

namespace App\Http\Controllers;

use App\Factory\DesignFactory;
use App\Models\Design;
use App\Repositories\DesignRepository;
use App\Requests\Design\StoreDesignRequest;
use App\Requests\Design\UpdateDesignRequest;
use App\Requests\SearchRequest;
use App\Search\DesignSearch;
use App\Transformations\DesignTransformable;
use Exception;

/**
 * Class DesignController
 * @package App\Http\Controllers
 */
class DesignController extends Controller
{
    use DesignTransformable;

    protected $design_repo;

    /**
     * DesignController constructor.
     * @param DesignRepository $design_repo
     */
    public function __construct(DesignRepository $design_repo)
    {
        $this->design_repo = $design_repo;
    }


    /**
     * @param SearchRequest $request
     * @return mixed
     */
    public function index(SearchRequest $request)
    {
        $designs = (new DesignSearch($this->design_repo))->filter($request, auth()->user()->account_user()->account);

        foreach ($designs as $key => $design) {
            $designs[$key]['design']->body = str_replace('$costs', '', $design['design']->body);
        }

        return response()->json($designs);
    }


    /**
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
    {
        $design = $this->design_repo->findDesignById($id);
        return response()->json($design);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function edit(int $id)
    {
        $design = $this->design_repo->findDesignById($id);
        return response()->json($design);
    }

    /**
     * @param int $id
     * @param UpdateDesignRequest $request
     * @return mixed
     */
    public function update(int $id, UpdateDesignRequest $request)
    {
        $design = $this->design_repo->findDesignById($id);

        $design = $this->design_repo->save($design, $request->all());

        return response()->json($this->transformDesign($design->fresh()));
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function create(int $id)
    {
        $design = DesignFactory::create(auth()->user()->company()->id, auth()->user()->id);

        return response()->json($design);
    }

    /**
     * @param StoreDesignRequest $request
     * @return mixed
     */
    public function store(StoreDesignRequest $request)
    {
        $design = DesignFactory::create(auth()->user()->account_user()->account_id, auth()->user()->id);

        $design = $this->design_repo->save($design, $request->all());

        return response()->json($this->transformDesign($design->fresh()));
    }


    /**
     * @param int $id
     * @return mixed
     * @throws Exception
     */
    public function destroy(int $id)
    {
        $design = Design::withTrashed()->where('id', '=', $id)->first();
        $design->deleteEntity();
        return response()->json([], 200);
    }
}
