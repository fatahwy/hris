<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;

use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\master\Client;
use app\models\master\Company;
use app\models\mdmsoft\Menu;
use app\widgets\Alert;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use richardfan\widget\JSRegister;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\BootstrapPluginAsset;
use yii\db\Query;
use yii\helpers\Url;

AppAsset::register($this);
BootstrapPluginAsset::register($this);

$js = JSRegister::begin();
?>
<script>
    $('#id_client_env, #id_company_env').on('change', function (e) {
        $.post('<?= Url::toRoute("/site/env") ?>', { [e.target.name]: e.target.value }, function (d) {
            location.reload();
        });
    });

    // Sidebar Toggle Logic
    $('.burger-menu, .sidebar-backdrop, .sidebar-nav .nav-link:not([data-bs-toggle])').on('click', function () {
        if ($(window).width() < 768) {
            $('body').toggleClass('sidebar-open');
        }
    });

    // Close sidebar on window resize if it was open
    $(window).on('resize', function () {
        if ($(window).width() >= 768) {
            $('body').removeClass('sidebar-open');
        }
    });

</script>
<?php
$js->end();

// Add custom CSS for a more modern grid view
$this->registerCss("
    .modern-grid-panel {
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        border: none;
        overflow: hidden;
    }
    .modern-grid-panel .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1rem 1.25rem;
    }
    .modern-grid-panel .card-header .kv-panel-before .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }
    .modern-grid-panel .card-header .kv-panel-before .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .table-modern th {
        background-color: #f8f9fa !important;
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
    .table-modern td {
        vertical-align: middle;
        padding: 1rem 0.75rem;
    }
");

// form card
$this->registerCss("
    .modern-form-card {
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        border: none;
    }
    .modern-form-card .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.5rem 1.5rem 1rem;
    }
    .modern-form-card .col-form-label {
        font-weight: 500;
        color: #4a5568;
    }
    .modern-form-card .form-control, .modern-form-card .form-select {
        border-radius: 8px;
        padding: 0.6rem 1rem;
        border: 1px solid #e2e8f0;
    }
    .modern-form-card .form-control:focus, .modern-form-card .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
");

// modern detail view
$this->registerCss("
    .table-modern-detail {
        margin-bottom: 0;
        color: #4a5568;
    }
    .table-modern-detail th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        width: 30%;
        vertical-align: middle;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .table-modern-detail td {
        vertical-align: middle;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
        background-color: #ffffff;
    }
    .table-modern-detail tr:last-child th,
    .table-modern-detail tr:last-child td {
        border-bottom: none;
    }
");

$user = GeneralHelper::identity();
$d = Yii::$app->cache->getOrSet('filteredMenusNested' . $user->id_user, function () use ($user) {
    $routes = (new Query())
        ->from(['aa' => 'auth_assignment'])
        ->innerJoin(['aic' => 'auth_item_child'], 'aa.item_name=aic.parent')
        ->innerJoin(['ai' => 'auth_item'], 'aic.child=ai.name')
        ->leftJoin(['aic2' => 'auth_item_child'], 'ai.name=aic2.parent')
        ->where(['aa.user_id' => $user->id_user])
        ->all();

    $filteredRoutes = [];
    foreach ($routes as $key => $value) {
        $d = !$value['child'] || str_contains($value['child'], '*') ? $value['route_menu'] : $value['child'];
        if ($d) {
            $filteredRoutes[$d] = $d;
        }
    }

    function nestedMenu($menus, $level)
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
                $res[$i]['items'] = nestedMenu($menus, $val['name']);
            } else {
                continue;
            }
        }

        return $res;
    }

    $menus = Menu::getMenuSource();

    if (!empty($filteredRoutes['all_access'])) {
        $d = nestedMenu($menus, null);
    } else {
        $filteredMenus = [];
        foreach ($menus as $value) {
            if ($value['name'] == 'Dashboard' || $value['route'] == null || !empty($filteredRoutes[$value['route']])) {
                $filteredMenus[] = $value;
            }
        }
        $filteredMenusNested = nestedMenu($filteredMenus, null);

        $d = [];
        foreach ($filteredMenusNested as $value) {
            if (!empty($value['url'][0]) || (empty($value['url'][0]) && $value['items'])) {
                $d[] = $value;
            }
        }
    }
    return $d;
});

// Register SweetAlert2 via CDN
$this->registerCssFile('https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['position' => \yii\web\View::POS_END, 'depends' => [\yii\web\JqueryAsset::class, \yii\web\YiiAsset::class]]);
$this->registerJs("
    yii.confirm = function (message, ok, cancel) {
        Swal.fire({
            title: 'Konfirmasi',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                ok && ok();
            } else {
                cancel && cancel();
            }
        });
        return false;
    };
", \yii\web\View::POS_READY);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <title>
        <?= Html::encode($this->title ?? 'Dashboard') ?>
    </title>
    <?php $this->head() ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body,
        html {
            height: 100%;
            background-color: #fbfcff;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        .sidebar-wrapper {
            width: 250px;
            background-color: #2b5fe1;
            color: #fff;
            border-top-right-radius: 40px;
            border-bottom-right-radius: 40px;
            position: fixed;
            top: 0;
            bottom: 0;
            left: -250px;
            /* Hidden on mobile */
            z-index: 1050;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.05);
            transition: left 0.3s ease;
        }

        @media (min-width: 768px) {
            .sidebar-wrapper {
                left: 0;
                border-radius: 0;
            }
        }

        body.sidebar-open .sidebar-wrapper {
            left: 0;
        }

        .sidebar-brand {
            padding: 25px;
            font-size: 1.3rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-upload {
            background-color: #fff;
            color: #2b5fe1;
            border-radius: 30px;
            padding: 10px 20px;
            font-weight: 600;
            border: none;
            margin: 0 25px 25px 25px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .sidebar-nav {
            flex-grow: 1;
            overflow-y: auto;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 5px;
        }

        .nav-item {
            padding: 12px 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .nav-item:hover,
        .nav-item.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            position: relative;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 10%;
            height: 80%;
            width: 4px;
            background-color: #fff;
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        .nav-sub-item {
            padding: 8px 25px 8px 48px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .nav-sub-item:hover {
            background-color: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .nav-sub-sub-item {
            padding: 6px 25px 6px 65px;
            font-size: 0.85rem;
        }

        .storage-details {
            padding: 25px;
            font-size: 0.8rem;
        }

        .storage-details .progress {
            height: 5px;
            background-color: rgba(255, 255, 255, 0.2);
            margin: 8px 0;
            border-radius: 10px;
            overflow: hidden;
            border: none;
        }

        .storage-details .progress-bar {
            background-color: #fff;
            border-radius: 10px;
        }

        .main-wrapper {
            margin-left: 0;
            /* No margin on mobile */
            display: flex;
            flex-direction: column;
            height: 100vh;
            transition: margin-left 0.3s ease;
        }

        @media (min-width: 768px) {
            .main-wrapper {
                margin-left: 250px;
            }
        }

        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            /* Reduced padding on mobile */
            background: #fff;
            border-bottom: 1px solid #f1f3f4;
            position: sticky;
            top: 0;
            z-index: 99;
        }

        @media (min-width: 768px) {
            .top-header {
                padding: 20px 40px;
                background: transparent;
            }
        }

        .burger-menu {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #f1f3f4;
            border-radius: 50%;
            cursor: pointer;
            color: #5f6368;
            flex-shrink: 0;
        }

        @media (min-width: 768px) {
            .burger-menu {
                display: none;
            }
        }

        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            display: none;
            backdrop-filter: blur(2px);
        }

        body.sidebar-open .sidebar-backdrop {
            display: block;
        }

        .header-selectors {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        @media (max-width: 767px) {
            .header-selectors {
                gap: 5px;
            }

            .header-selectors .select2-container {
                width: 140px !important;
            }

            .header-selectors label {
                font-size: 0.75rem;
            }
        }

        .search-container {
            background-color: #f1f3f4;
            border-radius: 30px;
            padding: 12px 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            width: 100%;
            max-width: 500px;
        }

        .search-container input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            font-size: 0.95rem;
            color: #5f6368;
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 18px;
            color: #5f6368;
        }

        .header-icons i {
            font-size: 1.2rem;
            cursor: pointer;
        }

        .user-block {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: 10px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #1a73e8;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .content-area {
            flex-grow: 1;
            padding: 15px 20px 20px 20px;
            overflow-y: auto;
        }

        @media (min-width: 768px) {
            .content-area {
                padding: 10px 40px 40px 40px;
            }
        }

        .content-area::-webkit-scrollbar {
            width: 8px;
        }

        .content-area::-webkit-scrollbar-thumb {
            background: #dadce0;
            border-radius: 4px;
        }

        .transition-caret {
            transition: transform 0.2s ease-in-out;
        }

        .nav-item[aria-expanded="true"] .transition-caret,
        .nav-sub-item[aria-expanded="true"] .transition-caret {
            transform: rotate(180deg);
        }
    </style>
</head>

<body>
    <?php $this->beginBody() ?>

    <div class="sidebar-wrapper">
        <div class="sidebar-brand">
            <i class="bi bi-triangle-half fs-4"></i>
            <span>
                <?= Yii::$app->name ?>
            </span>
        </div>


        <div class="sidebar-nav">
            <?php
            $route = '/' . Yii::$app->controller->route;

            if (!function_exists('renderMenu')) {
                function renderMenu($items, $route, $parentId = 'main')
                {
                    $html = '';
                    foreach ($items as $key => $item) {
                        $urlPath = rtrim($item['url'][0] ?? '/', '/');
                        if (empty($urlPath))
                            $urlPath = '/';
                        else if (!str_starts_with($urlPath, '/'))
                            $urlPath = '/' . $urlPath;

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

                                        $isSubActive = ($route == $subUrlRaw) || ($route == '/' && $subUrlRaw == '/site/index');
                                        $sActiveClass = $isSubActive ? 'active text-white' : '';

                                        $url = Url::to($subChild['url'][0] ?? '/');
                                        $html .= "
                                <a href=\"{$url}\" class=\"nav-sub-item nav-sub-sub-item {$sActiveClass}\">
                                    <i class=\"bi {$subChild['icon']}\"></i> {$subChild['label']}
                                </a>
                                ";
                                    }
                                    $html .= "</div>";
                                } else {
                                    $cActiveClass = $isCActive ? 'active text-white' : '';
                                    $url = Url::to($child['url'][0] ?? '/');
                                    $html .= "
                                <a href=\"{$url}\" class=\"nav-sub-item {$cActiveClass}\">
                                    <i class=\"bi {$child['icon']}\"></i> {$child['label']}
                                </a>
                                ";
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

            echo renderMenu($d, $route);
            ?>
        </div>

        <div class="sidebar-footer d-md-none border-top border-white-10 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 text-white">
                    <div class="user-avatar bg-white text-primary"
                        style="width: 32px; height: 32px; font-size: 0.8rem;">
                        <?php if (!empty($user->name))
                            echo strtoupper(substr($user->name, 0, 1)); ?>
                    </div>
                    <div class="d-flex flex-column" style="max-width: 120px;">
                        <span class="small fw-medium text-truncate"><?php if (!empty($user->name))
                            echo $user->name; ?></span>
                        <span class="small text-white-50" style="font-size: 0.7rem;">Logged in</span>
                    </div>
                </div>
                <?= Html::a('<i class="bi bi-box-arrow-right fs-5"></i>', ['/site/logout'], [
                    'data-method' => 'post',
                    'class' => 'text-white text-decoration-none p-2'
                ]) ?>
            </div>
        </div>
    </div>

    <div class="main-wrapper">
        <header class="top-header">
            <div class="d-flex align-items-center gap-3">
                <div class="burger-menu">
                    <i class="bi bi-list fs-4"></i>
                </div>

                <div class="header-selectors">
                    <?php
                    if (RoleHelper::isSuper()): ?>
                        <div class="d-flex flex-column" style="max-width: 250px;">
                            <label class="small text-muted mb-1" for="id_client_env">Client</label>
                            <?= Select2::widget([
                                'name' => 'id_client',
                                'value' => GeneralHelper::session('id_client'),
                                'data' => Client::getList(),
                                'options' => ['id' => 'id_client_env'],
                                'pluginOptions' => [
                                    'width' => '250px'
                                ]
                            ]); ?>
                        </div>
                    <?php endif; ?>

                    <?php
                    $list = $listCompany ?? Company::getList($user->id_client);
                    if (RoleHelper::allCompany() && count($list) > 1): ?>
                        <div class="d-flex flex-column" style="max-width: 250px;">
                            <label class="small text-muted mb-1" for="id_company_env">Perusahaan</label>
                            <?= Select2::widget([
                                'name' => 'id_company',
                                'value' => GeneralHelper::session('id_company'),
                                'data' => $listCompany ?? Company::getList($user->id_client),
                                'options' => ['id' => 'id_company_env'],
                                'pluginOptions' => [
                                    'width' => '250px'
                                ]
                            ]); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="header-icons d-none d-md-flex">
                <!-- <i class="bi bi-bell"></i> -->
                <!-- <i class="bi bi-question-circle"></i> -->
                <!-- <i class="bi bi-gear"></i> -->

                <div class="user-block">
                    <span class="text-dark fw-medium small">
                        <?php
                        if (!empty($user->name)) {
                            echo $user->name;
                        }
                        ?>
                    </span>
                    <div class="user-avatar shadow-sm">
                        <?php
                        if (!empty($user->name)) {
                            echo strtoupper(substr($user->name, 0, 1));
                        }
                        ?>
                    </div>
                </div>

                <div class="ms-3 d-flex gap-3 align-items-center">
                    <?= Html::a('<i class="bi bi-box-arrow-right"></i>', ['/site/logout'], ['data-method' => 'post', 'data-toggle' => 'tooltip', 'title' => 'Logout', 'data-confirm' => 'Apakah anda yakin akan logout?', 'class' => 'nav-link']) ?>
                </div>
            </div>
        </header>

        <main class="content-area">
            <!-- Content Header (Page header) -->
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h2 class="h4 mt-2 text-primary fw-bold d-flex align-items-center">
                        <?= $this->title ?>
                    </h2>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <div class="d-flex justify-content-end">
                        <?= Breadcrumbs::widget([
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                            'options' => [
                                'class' => 'breadcrumb'
                            ]
                        ]);
                        ?>
                    </div>
                </div><!-- /.col -->
            </div><!-- /.row -->
            <?= Alert::widget() ?>
            <?= $content ?>
        </main>
    </div>

    <div class="sidebar-backdrop"></div>
    <?php $this->endBody() ?>
</body>

</html><?php $this->endPage() ?>