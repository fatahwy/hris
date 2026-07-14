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
use kartik\select2\Select2;
use richardfan\widget\JSRegister;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\BootstrapPluginAsset;
use yii\helpers\Url;
use app\helpers\DBHelper;
use app\models\trx\Schedule;

$user = Yii::$app->user->identity;
$checkoutAlertSchedule = null;
if (!Yii::$app->user->isGuest && !Yii::$app->request->isAjax) {
    $currentRoute = Yii::$app->controller ? Yii::$app->controller->route : '';
    if (!in_array($currentRoute, ['trx/clock/index', 'site/logout', 'site/error'])) {
        $userId = Yii::$app->user->identity->id_user;
        $nowStr = DBHelper::now();

        // 1. Check if user needs to be redirected to check-in
        $pendingCheckin = Schedule::getAvailableSchedule();

        if ($pendingCheckin) {
            Yii::$app->response->redirect(['/trx/clock', 'id' => $pendingCheckin->id_schedule, 'type' => 'in'])->send();
            exit;
        }

        // 2. Check if user needs a checkout warning
        $checkoutAlertSchedule = $pendingCheckin ? Schedule::getActiveScheduleToClockOut() : null;
    }
}

AppAsset::register($this);
BootstrapPluginAsset::register($this);

$js = JSRegister::begin();
?>
<script>
    $('#id_client_env, #id_company_env').on('change', function(e) {
        $.post('<?= Url::toRoute("/site/env") ?>', {
            [e.target.name]: e.target.value
        }, function(d) {
            location.reload();
        });
    });

    // Sidebar Toggle Logic
    $('.burger-menu, .sidebar-backdrop, .sidebar-nav .nav-link:not([data-bs-toggle])').on('click', function() {
        if ($(window).width() < 768) {
            $('body').toggleClass('sidebar-open');
        }
    });

    // Close sidebar on window resize if it was open
    $(window).on('resize', function() {
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


$this->registerCssFile(
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
    ['depends' => [\yii\web\JqueryAsset::class]]
);

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
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .user-block:hover {
            opacity: 0.85;
        }

        .user-profile-link {
            transition: opacity 0.2s;
        }

        .user-profile-link:hover {
            opacity: 0.85;
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

        <div class="sidebar-nav" id="main">
            <?= Menu::renderMenu('main') ?>
        </div>

        <div class="sidebar-footer d-md-none border-top border-white-10 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <a href="<?= Url::to(['/profile']) ?>"
                    class="d-flex align-items-center gap-2 text-white text-decoration-none user-profile-link">
                    <div class="user-avatar bg-white text-primary"
                        style="width: 32px; height: 32px; font-size: 0.8rem;">
                        <?= strtoupper(substr($user->name ?? '', 0, 1)) ?>
                    </div>
                    <div class="d-flex flex-column" style="max-width: 120px;">
                        <span class="small fw-medium text-truncate"><?= $user->name ?? '' ?></span>
                        <span class="small text-white-50" style="font-size: 0.7rem;">Logged in</span>
                    </div>
                </a>
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

                <a href="<?= Url::to(['/profile']) ?>" class="user-block">
                    <span class="text-dark fw-medium small">
                        <?= $user->name ?? '' ?>
                    </span>
                    <div class="user-avatar shadow-sm">
                        <?= strtoupper(substr($user->name ?? '', 0, 1)) ?>
                    </div>
                </a>

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
            <?php if ($checkoutAlertSchedule): ?>
                <div class="alert alert-warning d-flex flex-column flex-md-row align-items-start align-items-md-center alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4"
                    role="alert">
                    <div class="d-flex align-items-start align-items-md-center flex-grow-1 mb-3 mb-md-0">
                        <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning"></i>
                        <div>
                            <strong>Peringatan Checkout!</strong> Jam kerja shift Anda
                            (<?= Html::encode($checkoutAlertSchedule->shift_name) ?>) telah berakhir pada pukul
                            <?= date('H:i', strtotime($checkoutAlertSchedule->workhour_end)) ?>. Harap segera lakukan
                            checkout kehadiran.
                        </div>
                    </div>
                    <div class="ms-5 ms-md-0 me-md-4">
                        <?= Html::a('<i class="bi bi-box-arrow-left me-1"></i> Checkout Sekarang', ['/trx/clock', 'id' => $checkoutAlertSchedule->id_schedule, 'type' => 'out'], ['class' => 'btn btn-warning btn-sm text-dark fw-bold rounded-2 px-3']) ?>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </main>
    </div>

    <div class="sidebar-backdrop"></div>
    <?php $this->endBody() ?>
</body>

</html><?php $this->endPage() ?>