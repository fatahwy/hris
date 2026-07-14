<?php

namespace app\models\mdmsoft;

use Yii;
use yii\db\Query;
use yii\helpers\Url;

class Menu extends \mdm\admin\models\Menu
{

    public function rules()
    {
        return [
            [['name'], 'required'],
            [
                ['parent_name'],
                'in',
                'range' => static::find()->select(['name'])->column(),
                'message' => 'Menu "{value}" not found.'
            ],
            [['parent', 'route', 'data', 'order'], 'default'],
            [
                ['parent'],
                'filterParent',
                'when' => function () {
                    return !$this->isNewRecord;
                }
            ],
            [['order'], 'integer'],
            // [
            //     ['route'],
            //     'in',
            //     'range' => static::getSavedRoutes(),
            //     'message' => 'Route "{value}" not found.'
            // ]
        ];
    }

    public function attributeLabels()
    {
        return [];
    }

    public static function getMenuSource()
    {
        $tableName = static::tableName();
        $query = (new Query())
            ->select(['m.id', 'm.name', 'm.route', 'm.icon', 'parent_name' => 'p.name'])
            ->from(['m' => $tableName])
            ->leftJoin(['p' => $tableName], '[[m.parent]]=[[p.id]]')
            ->where(['m.stat' => 1])
            ->orderBy('m.order')
            ->cache()
            ->all(static::getDb());

        return $query;
    }

    private static function nestedMenu($menus, $level)
    {
        $res = [];
        foreach ($menus as $i => $val) {
            if ($val['parent_name'] == $level) {
                if ($val['route'] && str_contains($val['route'], 'MstKpi[number_period]')) {
                    $val['route'] = str_replace('MstKpi[number_period]', 'MstKpi[number_period]=' . date('n'), $val['route']);
                }
                $res[$i] = [];
                $res[$i]['label'] = $val['name'];
                $res[$i]['icon'] = $val['icon'] ?: '';
                $res[$i]['url'] = [$val['route']];
                $res[$i]['items'] = self::nestedMenu($menus, $val['name']);
            } else {
                continue;
            }
        }

        return $res;
    }

    public static function renderMenu()
    {
        $parentId = 'main';
        $route = '/' . Yii::$app->controller->route;
        $user = Yii::$app->user;

        $routes = (new Query())
            ->from(['aa' => 'auth_assignment'])
            ->innerJoin(['aic' => 'auth_item_child'], 'aa.item_name=aic.parent')
            ->innerJoin(['ai' => 'auth_item'], 'aic.child=ai.name')
            ->leftJoin(['aic2' => 'auth_item_child'], 'ai.name=aic2.parent')
            ->where(['aa.user_id' => $user->identity->id_user])
            ->all();

        $filteredRoutes = [];
        foreach ($routes as $value) {
            $rute = !$value['child'] || str_contains($value['child'], '*') ? $value['route_menu'] : $value['child'];

            if ($value['route_menu'] == 'dashboard' && str_contains($value['child'], '*')) {
                $temp = str_replace('*', 'index', $value['child']);
                $filteredRoutes[$temp] = $temp;
            }
            if ($rute) {
                $filteredRoutes[$rute] = $rute;
            }
        }

        $menus = self::getMenuSource();

        if (!empty($filteredRoutes['all_access'])) {
            $items = self::nestedMenu($menus, null);
        } else {
            $filteredMenus = [];
            foreach ($menus as $value) {
                if ($value['name'] == 'Dashboard' || $value['route'] == null || !empty($filteredRoutes[$value['route']])) {
                    $filteredMenus[] = $value;
                }
            }
            $filteredMenusNested = self::nestedMenu($filteredMenus, null);

            $items = [];
            foreach ($filteredMenusNested as $value) {
                if (!empty($value['url'][0]) || (empty($value['url'][0]) && $value['items'])) {
                    $items[] = $value;
                }
            }
        }

        $html = '';
        foreach ($items as $key => $item) {
            $urlPath = rtrim($item['url'][0] ?? '/', '/');
            if (empty($urlPath))
                $urlPath = '/';
            else if (!str_starts_with($urlPath, '/'))
                $urlPath = '/' . $urlPath;

            // if (!in_array($urlPath, ['/site/index', '/'])) {
            //     if (!GeneralHelper::checkRoute($urlPath, Yii::$app->getRequest()->get(), $user)) {
            //         continue;
            //     }
            // }

            $isActive = ($route == $urlPath) || ($route == '/' && $urlPath == '/site/index');

            // Check if any child is active
            $isChildActive = false;
            if (!empty($item['items'])) {
                $checkActive = function ($children) use (&$checkActive, $route) {
                    foreach ($children as $child) {
                        $cUrl = rtrim($child['url'][0] ?? '/', '/');
                        if (empty($cUrl))
                            $cUrl = '/';
                        else if (!str_starts_with($cUrl, '/'))
                            $cUrl = '/' . $cUrl;
                        if ($route == $cUrl || (!empty($child['items']) && $checkActive($child['items']))) {
                            return true;
                        }
                    }
                    return false;
                };
                $isChildActive = $checkActive($item['items']);
            }

            $activeClass = ($isActive || $isChildActive) ? 'active' : '';
            $collapseId = "collapse_{$parentId}_{$key}";

            if (!empty($item['items'])) {
                $isExpanded = $isChildActive ? 'true' : 'false';
                $showClass = $isChildActive ? 'show' : '';

                $html .= "<a href=\"#{$collapseId}\" data-bs-toggle=\"collapse\" aria-expanded=\"{$isExpanded}\" class=\"nav-item {$activeClass} justify-content-between px-3\">
                                        <div class=\"d-flex align-items-center gap-3\">
                                            <i class=\"bi {$item['icon']}\"></i> {$item['label']}
                                        </div>
                                        <i class=\"bi bi-caret-down-fill text-white-50 transition-caret\" style=\"font-size: 0.75rem;\"></i>
                                    </a>";
                $html .= "<div class=\"collapse {$showClass} mb-2\" id=\"{$collapseId}\">";
                foreach ($item['items'] as $childKey => $child) {
                    $cUrlPath = rtrim($child['url'][0] ?? '/', '/');
                    if (empty($cUrlPath))
                        $cUrlPath = '/';
                    else if (!str_starts_with($cUrlPath, '/'))
                        $cUrlPath = '/' . $cUrlPath;

                    // Check route access for child
                    // if (!GeneralHelper::checkRoute($cUrlPath, Yii::$app->getRequest()->get(), $user)) {
                    //     continue;
                    // }

                    $isCActive = ($route == $cUrlPath) || ($route == '/' && $cUrlPath == '/site/index');

                    $hasSubChildren = !empty($child['items']);
                    $isSubChildActive = false;

                    if ($hasSubChildren) {
                        $subCollapseId = "collapse_{$collapseId}_{$childKey}";
                        $isSubChildActive = $checkActive($child['items']);
                        $isCActive = $isCActive || $isSubChildActive;
                        $cActiveClass = $isCActive ? 'active' : '';
                        $isSubExpanded = $isSubChildActive ? 'true' : 'false';
                        $subShowClass = $isSubChildActive ? 'show' : '';

                        $html .= "<a href=\"#{$subCollapseId}\" data-bs-toggle=\"collapse\" aria-expanded=\"{$isSubExpanded}\" class=\"nav-sub-item {$cActiveClass} justify-content-between\">
                                                <div class=\"d-flex align-items-center gap-2\">
                                                    <i class=\"bi {$child['icon']}\"></i> {$child['label']}
                                                </div>
                                                <i class=\"bi bi-caret-down-fill text-white-50 transition-caret\" style=\"font-size: 0.75rem;\"></i>
                                            </a>";
                        $html .= "<div class=\"collapse {$subShowClass}\" id=\"{$subCollapseId}\">";
                        foreach ($child['items'] as $subKey => $subChild) {
                            $subUrlRaw = rtrim($subChild['url'][0] ?? '/', '/');
                            if (empty($subUrlRaw))
                                $subUrlRaw = '/';
                            else if (!str_starts_with($subUrlRaw, '/'))
                                $subUrlRaw = '/' . $subUrlRaw;

                            // Check route access for sub-child
                            // if (!GeneralHelper::checkRoute($subUrlRaw, Yii::$app->getRequest()->get(), $user)) {
                            //     continue;
                            // }

                            $isSubActive = ($route == $subUrlRaw) || ($route == '/' && $subUrlRaw == '/site/index');
                            $sActiveClass = $isSubActive ? 'active text-white' : '';

                            $url = Url::to($subChild['url'][0] ?? '/');
                            $html .= "
                                                <a href=\"{$url}\" class=\"nav-sub-item nav-sub-sub-item {$sActiveClass}\">
                                                    <i class=\"bi {$subChild['icon']}\"></i> {$subChild['label']}
                                                </a>";
                        }
                        $html .= "</div>";
                    } else {
                        $cActiveClass = $isCActive ? 'active text-white' : '';
                        $url = Url::to($child['url'][0] ?? '/');
                        $html .= "
                                                <a href=\"{$url}\" class=\"nav-sub-item {$cActiveClass}\">
                                                    <i class=\"bi {$child['icon']}\"></i> {$child['label']}
                                                </a>";
                    }
                }
                $html .= "</div>";
            } else {
                $url = Url::to($item['url']['0'] ?? '/');
                $html .= "<a href=\"{$url}\" class=\"nav-item px-3 {$activeClass}\">
                                        <i class=\"bi {$item['icon']}\"></i> {$item['label']}
                                    </a>";
            }
        }
        return $html;
    }
}
