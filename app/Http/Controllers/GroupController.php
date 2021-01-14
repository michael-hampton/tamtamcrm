<?php

namespace App\Http\Controllers;

use App\Factory\GroupFactory;
use App\Http\Requests\SignupRequest;
use App\Models\Group;
use App\Repositories\GroupRepository;
use App\Requests\Group\StoreGroupRequest;
use App\Requests\Group\UpdateGroupRequest;
use App\Requests\SearchRequest;
use App\Search\GroupSearch;
use App\Settings\GroupSettings;
use App\Traits\UploadableTrait;
use App\Transformations\GroupTransformable;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class GroupSettingController
 * @package App\Http\Controllers
 */
class GroupController extends Controller
{
    use DispatchesJobs;
    use UploadableTrait;
    use GroupTransformable;

    protected GroupRepository $group_setting_repo;

    /**
     * GroupSettingController constructor.
     * @param GroupRepository $group_setting_repo
     */
    public function __construct(GroupRepository $group_setting_repo)
    {
        $this->group_setting_repo = $group_setting_repo;
    }

    /**
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function index(SearchRequest $request)
    {
        $group_settings = (new GroupSearch($this->group_setting_repo))->filter(
            $request,
            auth()->user()->account_user()->account
        );

        return response()->json($group_settings);
    }

    /**
     * @param StoreGroupRequest $request
     * @return JsonResponse
     */
    public function store(StoreGroupRequest $request)
    {
        $group_setting = GroupFactory::create(auth()->user()->account_user()->account, auth()->user());
        $group_setting = $this->group_setting_repo->save($request->except('settings'), $group_setting);
        $group_setting = (new GroupSettings)->save($group_setting, $request->settings);

        if (!$group_setting) {
            return response()->json('Unable to save group');
        }

        return response()->json($this->transformGroup($group_setting));
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id)
    {
        return response()->json($this->transformGroup($this->group_setting_repo->findGroupById($id)));
    }

    /**
     * @param int $id
     * @param UpdateGroupRequest $request
     * @return JsonResponse
     */
    public function update(int $id, UpdateGroupRequest $request)
    {
        $group_setting = $this->group_setting_repo->findGroupById($id);
        $group_setting = $this->group_setting_repo->save($request->except('settings'), $group_setting);
        $settings = json_decode(json_encode($request->input('settings')));

        if ($request->company_logo !== null && $request->company_logo !== 'null') {
            $logo_path = $this->uploadLogo($request->file('company_logo'));
            $settings->company_logo = $logo_path;
        }

        $group_setting = (new GroupSettings)->save($group_setting, $settings);
        return response()->json($this->transformGroup($group_setting));
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function archive(int $id)
    {
        $group = $this->group_setting_repo->findGroupById($id);
        $group->archive();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(int $id)
    {
        $group = Group::withTrashed()->where('id', '=', $id)->first();
        $this->authorize('delete', $group);
        $group->deleteEntity();
        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function filterGroups(Request $request)
    {
        $quotes = (new GroupSearch($this->group_setting_repo))->filterBySearchCriteria(
            $request->all(),
            auth()->user()->account_user()->account_id
        );
        return response()->json($quotes);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $group = Group::withTrashed()->where('id', '=', $id)->first();
        $group->restoreEntity();
        return response()->json([], 200);
    }
}
