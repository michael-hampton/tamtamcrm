<?php

namespace App\Http\Controllers;

use App\Jobs\Attribute\SaveAttributeValues;
use App\Models\Attribute;
use App\Repositories\AttributeRepository;
use App\Requests\Attribute\CreateAttributeRequest;
use App\Requests\Attribute\UpdateAttributeRequest;
use App\Requests\SearchRequest;
use App\Search\AttributeSearch;
use App\Transformations\AttributeTransformable;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AttributeController extends Controller
{
    private $attribute_repo;

    /**
     * AttributeController constructor.
     * @param AttributeRepository $attributeRepository
     */
    public function __construct(AttributeRepository $attributeRepository)
    {
        $this->attribute_repo = $attributeRepository;
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $attributes = (new AttributeSearch($this->attribute_repo))->filter(
            $request
        );
        return response()->json($attributes);
    }

    /**
     * @return void
     */
    public function create()
    {
        //return view('admin.attributes.create');
    }

    /**
     * @param CreateAttributeRequest $request
     * @return JsonResponse
     */
    public function store(CreateAttributeRequest $request)
    {
        $attribute = $this->attribute_repo->save(new Attribute(), $request->except('values'));
        SaveAttributeValues::dispatchNow($attribute, $request->input('values'));
        return response()->json((new AttributeTransformable)->transformAttribute($attribute));
    }

    /**
     * @param $id
     * @return Factory|View
     */
    public function show($id)
    {
        $attribute = $this->attribute_repo->findAttributeById($id);
        $attribute_repo = new AttributeRepository($attribute);

        return response()->json(
            [
                'attribute' => $attribute,
                'values'    => $attribute_repo->listAttributeValues()
            ]
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        $attribute = $this->attribute_repo->findAttributeById($id);

        return response()->json($attribute);
    }

    /**
     * @param UpdateAttributeRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(UpdateAttributeRequest $request, $id)
    {
        $attribute = $this->attribute_repo->findAttributeById($id);

        $attribute_repo = new AttributeRepository($attribute);
        $attribute_repo->save($attribute, $request->all());
        SaveAttributeValues::dispatchNow($attribute, $request->input('values'));
        return response()->json((new AttributeTransformable)->transformAttribute($attribute));
    }

    /**
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    public function destroy($id)
    {
        $this->attribute_repo->findAttributeById($id)->delete();

        return response()->json('Attribute deleted successfully!');
    }
}
