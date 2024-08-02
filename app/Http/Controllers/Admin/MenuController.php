<?php
namespace App\Http\Controllers\Admin;

use App\Constants\AdminStatusCodes;
use App\Exceptions\AdminException;
use App\Http\Requests\Admin\MenuDeleteRequest;
use App\Http\Requests\Admin\MenuIndexRequest;
use App\Http\Requests\Admin\MenuUpdateRequest;
use App\Models\Admin\Menu;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;

/**
 * 菜单控制器
 * @Auther Qasim
 * @date 2023/6/29
 */
class MenuController extends BaseController
{
    /**
     * 菜单列表
     * @param MenuIndexRequest $request
     * @return JsonResponse
     */
    public function index(MenuIndexRequest $request): JsonResponse
    {

        // 查询结果缓存
        if($menus = $this->redis->get('menus1')){

            $menus = json_decode($menus, true);
        }else{
            // 定义要传递的作用域
            $scopes = ['sort', 'fields'];

            Menu::setFields(['id', 'pid', 'name', 'component', 'sort', 'icon', 'visible', 'status']);

            // 设置模型的作用域
            Menu::setScopes($scopes);

            $menus = Menu::scopes(array_merge($scopes, ['root']))->with(['allChildren'])->get()?->toArray();

            $this->redis->set('menus', json_encode($menus));
        }

        if($request->name || is_numeric($request->status)) {

            $menus = $this->filterMenus($menus, $request->name, $request->status);

            $menus = $menus->toArray();
        }

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('fetch_success'), $menus);
    }

    /**
     * 修改菜单
     * @param MenuUpdateRequest $request
     * @return JsonResponse
     */
    public function update(MenuUpdateRequest $request): JsonResponse
    {

        $menu = Menu::find($request->id);

        $menu->icon = $request->icon ?? $menu->icon;
        $menu->sort = $request->sort ?? $menu->sort;
        $menu->save();

        //删除缓存
        $this->redis->del('menus');

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('update_success'));
    }

    /**
     * 删除菜单
     * @param MenuDeleteRequest $request
     * @return JsonResponse
     * @throws AdminException
     */
    public function delete(MenuDeleteRequest $request): JsonResponse
    {

        $menu = Menu::find($request->id);

        //存在下级不能删除
        if($menu->allChildren->count()){

            throw new AdminException($this->getMessage('menu_has_children'), AdminStatusCodes::MENU_HAS_CHILDREN);
        }

        $menu->delete();

        //删除缓存
        $this->redis->del('menus');

        return json(AdminStatusCodes::SUCCESS, $this->getMessage('delete_success'));
    }

    /**
     * 递归过滤菜单，保持层级结构。
     *
     * @param array $menus 菜单
     * @param string|null $name 菜单名称
     * @param int|null $status 菜单状态
     * @return Collection 过滤后的菜单
     */
    private function filterMenus(array $menus, ?string $name, ?int $status): Collection
    {
        return collect($menus)->map(function ($menu) use ($name, $status) {
            // 初始化匹配结果
            $menu['matched'] = false;

            if(is_numeric($status) && $menu['status'] == $status){
                $menu['matched'] = true;
            }

            if ($name && str_contains($menu['name'], $name)) {
                $menu['matched'] = true;
            }

            if(is_numeric($status) && $name){

                $menu['matched'] = ($menu['status'] == $status) && str_contains($menu['name'], $name);
            }

            // 如果有子菜单，递归过滤子菜单
            if (isset($menu['all_children']) && is_array($menu['all_children'])) {
                $menu['all_children'] = $this->filterMenus($menu['all_children'], $name, $status);

                if($name) {
                    // 如果子菜单中存在匹配，当前菜单也被认为匹配
                    $menu['matched'] = $menu['matched'] || $menu['all_children']->contains('matched', true);
                }
            }

            return $menu;
        })->filter(function ($menu) {
            //过滤未匹配的菜单
            return $menu['matched'];
        })->values();
    }
}
