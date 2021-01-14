<?php

namespace App\Http\Controllers;

use App\Models\AttributeValue;
use App\Repositories\AttributeRepository;
use App\Repositories\AttributeValueRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class AttributeValueController extends Controller
{
    /**
     * @var AttributeRepository
     */
    private AttributeRepository $attribute_repo;

    /**
     * @var AttributeValueRepository
     */
    private AttributeValueRepository $attribute_value_repo;

    /**
     * AttributeValueController constructor.
     * @param AttributeRepository $attributeRepository
     * @param AttributeValueRepository $attributeValueRepository
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        AttributeValueRepository $attributeValueRepository
    ) {
        $this->attribute_repo = $attributeRepository;
        $this->attribute_value_repo = $attributeValueRepository;
    }

    /**
     * @return JsonResponse
     */
    public function index()
    {
        $attributes = $this->attribute_value_repo->listAttributeValues();
        //$attributes = $this->attribute_repo->paginateArrayResults($results->all());

        return response()->json($attributes);
    }

    public function create($id)
    {
        return response()->json(
            [
                'attribute' => $this->attribute_repo->findAttributeById($id)
            ]
        );
    }

    /**
     * @param CreateAttributeValueRequest $request
     * @param $id
     * @return JsonResponse
     */
    function store(CreateAttributeValueRequest $request, $id)
    {
        $attribute = $this->attribute_repo->findAttributeById($id);

        $attribute_value = new AttributeValue();
        $attribute_value->fill($request->all());
        $attribute_value_repo = new AttributeValueRepository($attribute_value);

        $attribute_value_repo->associateToAttribute($attribute);

        return response()->json($attribute_value);
    }

    /**
     * @param $attribute_id
     * @param $attribute_value_id
     * @return RedirectResponse
     */
    public function destroy($attribute_id, $attribute_value_id)
    {
        $attribute_value = $this->attribute_value_repo->findOneOrFail($attribute_value_id);

        $attribute_value_repo = new AttributeValueRepository($attribute_value);
        $attribute_value_repo->dissociateFromAttribute();

        return response()->json($attribute_value);
    }
}
