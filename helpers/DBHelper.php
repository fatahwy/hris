<?php

namespace app\helpers;

use app\models\AuthItem;
use app\models\AuthItemChild;
use app\models\mdmsoft\Menu;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class DBHelper
{

    public static function today()
    {
        return date('Y-m-d');
    }

    public static function now()
    {
        return date('Y-m-d H:i:s');
    }

    public static function toSqlDate($date, $format = 'Y-m-d')
    {
        if (!empty($date)) {
            return date($format, strtotime($date));
        }
        return null;
    }

    public static function toHumanDate($date)
    {
        if (!empty($date)) {
            return date('d-m-Y', strtotime($date));
        }
        return null;
    }

    public static function getPageCache($parent, $sql, $routes = ['index'])
    {
        $pageCache = [
            'class' => 'yii\filters\PageCache',
            'only' => $routes,
            'duration' => 3600,
            'variations' => [
                Yii::$app->language,
            ],
            'dependency' => [
                'class' => 'yii\caching\DbDependency',
                'sql' => $sql,
            ],
        ];

        return array_merge($parent, [$pageCache]);
    }

    public static function initMenu()
    {
        $dashboard = 1;
        $report = 2;
        $master = 3;
        $data = 4;
        $trx = 5;
        $setting = 6;
        $log = 7;

        $defIcon = 'bi-border-width';
        // name, parent, route, stat
        $menu[$dashboard] = ['Dashboard', null, '/site/index', 1, 'bi-house'];
        $menu[$report] = ['Laporan', null, null, 1, 'bi-bar-chart'];
        $menu[$master] = ['Master', null, null, 1, 'bi-file-earmark-text'];
        $menu[$trx] = ['Transaksi', null, null, 1, 'bi-activity'];
        $menu[$setting] = ['Setting', null, null, 1, 'bi-gear'];
        $menu[$log] = ['Log', null, '/log', 1, 'bi-hdd-stack'];

        $submenu = [
            // Laporan
            ['Izin & Cuti', $report, '/report/leave/index', 1, $defIcon],
            ['Payroll', $report, '/report/payroll/index', 1, $defIcon],
            // Master
            ['Client', $master, '/master/client/index', 1, $defIcon],
            ['Perusahaan', $master, '/master/company/index', 1, $defIcon],
            ['Departemen', $master, '/master/department/index', 1, $defIcon],
            ['Jabatan', $master, '/master/position/index', 1, $defIcon],
            ['User', $master, '/master/user/index', 1, $defIcon],
            ['Izin & Cuti', $master, '/master/leave-type/index', 1, $defIcon],
            ['Shift', $master, '/master/shift/index', 1, $defIcon],
            ['Role', $master, '/master/role/index', 1, $defIcon],
            // Data           
            // Trx
            ['Presensi', $trx, '/trx/attendance/index', 1, $defIcon],
            ['Jadwal kerja', $trx, '/trx/schedule/index', 1, $defIcon],
            ['Izin & Cuti', $trx, '/trx/leave-request/index', 1, $defIcon],
            ['Payroll', $trx, '/trx/payroll/index', 1, $defIcon],
            // Setting
            ['Hak Akses Menu', $setting, '/setting/access-rule/index', 1, $defIcon],
        ];

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $flag = true;
            $counter = 0;

            Yii::$app->db->createCommand("truncate table menu")->execute();
            foreach ($menu as $i => $d) {
                $model = new Menu();
                $model->id = $i;
                $model->name = $d[0];
                $model->parent = $d[1];
                $model->route = $d[2];
                $model->stat = $d[3];
                $model->icon = $d[4];
                $model->order = $i;

                if (($flag = $model->save()) == false) {
                    $transaction->rollBack();
                    break;
                }
                $counter = $i;
            }

            foreach ($submenu as $i => $d) {
                $id = $counter + $i + 1;
                $model = new Menu();
                $model->id = $id;
                $model->name = $d[0];
                $model->parent = $d[1];
                $model->route = $d[2];
                $model->stat = $d[3];
                $model->icon = $d[4];
                $model->order = $id;

                if (($flag = $model->save()) == false) {
                    $transaction->rollBack();
                    echo '<pre>';
                    print_r($model);
                    die;
                }
            }

            if ($flag) {
                $transaction->commit();
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }

        // ACCESS RULE
        $authItems = [
            // Super
            'all_access' => " Super Akses|Super Admin",
            'all_company' => " Super Akses|Lintas Perusahaan",
            'all_user' => " Super Akses|Semua Data",
            // Dashboard
            'dashboard' => '',
            // Laporan
            '/report/sale/*' => "Report|Penjualan",
            '/report/expense/*' => "Report|Pengeluaran",
            '/report/income/*' => "Report|Pemasukan",
            '/report/buku-besar/*' => "Report|Buku Besar",
            '/report/neraca-lajur/*' => "Report|Neraca Lajur",
            '/report/laba-rugi-akuntansi/*' => "Report|Laba Rugi Akuntansi",
            '/report/laba-rugi/*' => "Report|Laba - Rugi",
            '/report/pareto/*' => "Report|Pareto",
            '/report/probability/*' => "Report|Probabilitas",
            // Master
            '/master/client/*' => "Master|Client",
            '/master/company/*' => "Master|Perusahaan",
            '/master/department/*' => "Master|Departemen",
            '/master/position/*' => "Master|Jabatan",
            '/master/leave-type/*' => "Master|Tipe Cuti",
            '/master/shift/*' => "Master|Shift",
            '/master/user/*' => "Master|User",
            // Data
            // Transaksi
            '/trx/attendance/*' => "Transaksi|Presensi",
            '/trx/schedule/*' => "Transaksi|Jadwal Kerja",
            '/trx/leave-request/*' => "Transaksi|Izin & Cuti",
            'approval_leave' => "Transaksi|Approval Izin & Cuti",
            '/trx/payroll/*' => "Transaksi|Payroll",
            'approval_payroll' => "Transaksi|Approval Payroll",
            // Setting
            '/setting/access-rule/*' => "Setting|Hak Akses Menu",
        ];

        $authItemChilds = [
            'dashboard' => [
                "/json/*",
                "/gridview/*",
                "/site/*",
                "/profile/*",
            ],
            'all_access' => [
                "/*",
            ],
        ];

        // AuthItem:1: MasterRole
        // AuthItem:2: Master Menu Akses
        // AuthItemChild: Role assing Menu Akses 
        // AuthAssignment: Role assign User

        AuthItem::updateAll(['description' => null]);
        $i = 0;
        foreach ($authItems as $route => $data) {
            $model = AuthItem::findOne(['name' => $route, 'type' => 2]);

            if (is_array($data)) {
                $description = $data['label'];
                $route_menu = $data['route_menu'];
            } else {
                $route_menu = $route;
                $description = $data;
                if (strpos($route, '*') !== false) {
                    $route_menu = str_replace('*', 'index', $route);
                }
            }

            if ($model) {
                $model->description = $description;
            } else {
                $model = new AuthItem();
                $model->name = $route;
                $model->type = 2;
                $model->description = $description;
            }
            $model->label = '-';
            $model->route_menu = $route_menu;
            $model->order_val = $i;
            $model->save();
            $i++;
        }

        foreach ($authItemChilds as $parent => $arrRoute) {
            AuthItemChild::deleteAll(['parent' => $parent]);
            $mList = ArrayHelper::map(AuthItem::findAll(['name' => $arrRoute, 'type' => 2]), 'name', 'name');

            foreach ($arrRoute as $child) {
                if (empty($mList[$child])) {
                    $m = new AuthItem();
                    $m->name = $child;
                    $m->type = 2;
                    $m->save();
                }

                $model = new AuthItemChild();
                $model->parent = $parent;
                $model->child = $child;
                if (($flag = $model->save()) == false) {
                    echo '<pre>';
                    print_r($model);
                    die;
                }
            }
        }

        // foreach (AuthItem::find()->where(['type' => 1])->asArray()->all() as $m) {
        //     self::updateBaseRoute($m['name']);
        // }

        // GeneralHelper::cacheFlush();
    }

    public static function updateBaseRoute($role)
    {
        $newRoutes = [];

        foreach ($newRoutes as $route) {
            Yii::$app->db->createCommand()->upsert(
                'auth_item_child',
                [
                    'parent' => $role,
                    'child' => $route,
                ]
            )->execute();

            $ai = AuthItem::findOne(['name' => $route, 'route_menu' => null]);
            if ($ai) {
                $ai->route_menu = $route;
                $ai->save();
            }
        }
    }

    public static function initView()
    {
        // USER CHECKIN ACTIVE
        $queryUserCheckinActive = "
                SELECT user.user_id, username, nip, s.id_branch,s.workhour_start, s.workhour_end, s.id_schedule, s.datetime_open, s.datetime_close FROM `user`
                INNER JOIN `trs_schedule` s ON `user`.`user_id` = `s`.`user_id` 
                WHERE s.`workhour_start` <= NOW() AND s.`workhour_end` > NOW() AND datetime_open IS NOT NULL AND datetime_close IS NULL;";
        // END USER CHECKIN ACTIVE

        Yii::$app->db->createCommand("CREATE OR REPLACE VIEW user_active AS $queryUserCheckinActive")->execute();
    }
}
