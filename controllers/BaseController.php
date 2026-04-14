<?php

namespace app\controllers;

use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\master\Company;
use app\queue\CreateLog;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class BaseController extends Controller
{

    public $id_company;
    public $id_client;
    public $user;

    public function behaviors()
    {
        $accessControl = [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login', 'register', 'forgot-password', 'new-password', 'activate'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'logout' => ['post'],
                ],
            ],
        ];

        return array_merge(parent::behaviors(), $accessControl);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($action)
    {
        $user = Yii::$app->user->identity;
        $this->user = $user;
        if (!Yii::$app->user->isGuest) {
            $this->id_client = GeneralHelper::session('id_client') ?? $user->id_client;
            $this->id_company = GeneralHelper::session('id_company') ?? $user->id_company;
        }

        if (parent::beforeAction($action)) {
            // $this->doLog($action);
            return true;
        }

        return false;
    }

    public function checkAllBranch($idCompany = null, $keepExist = false, $isAllCompany = null)
    {
        $isAllCompany = is_null($isAllCompany) ? RoleHelper::allCompany() : $isAllCompany;
        $user = Yii::$app->user->identity;
        $userIdBranch = $user->id_company;
        if (empty($idCompany)) {
            return $keepExist || !$isAllCompany ? $userIdBranch : null;
        }

        $model = Yii::$app->cache->getOrSet("listCompany$idCompany", function () use ($idCompany) {
            return Company::findOne($idCompany);
        });
        if (!$model) {
            throw new ForbiddenHttpException('Perusahaan tidak ditemukan');
        }
        if (!$isAllCompany && $idCompany != $userIdBranch) {
            throw new ForbiddenHttpException('Anda tidak memiliki akses di perusahaan ' . $model->name);
        }

        return $idCompany;
    }

    private function doLog($action)
    {
        $req = Yii::$app->request;
        $user = Yii::$app->user->identity;

        if ($req->isPost && (!$req->isAjax || (in_array($action->controller->route, ['trx/cashier/create', 'trx/cashier/receipt', 'trx/cashier/pending', 'trx/cashier/retur']) && !$req->post('ajax')))) {
            $route = $action->controller->route;
            Yii::$app->queue->push(new CreateLog(compact('route', 'req', 'user')));
        }
    }

    protected function isEmptyCell($sheet, $cell, $cellMsg, $msg = '', $isDate = false)
    {
        $val = $sheet->getCell($cell)->getValue();

        if (strlen(trim($val)) <= 0) {
            $tmpError = [];
            $valMsg = $sheet->getCell($cellMsg)->getValue();
            if (!empty($valMsg)) {
                $tmpError[] = $valMsg;
            }
            $tmpError[] = "$msg harus diisi";
            $sheet->getCell($cellMsg)->setValue(implode(', ', $tmpError));
        }
        if ($isDate && is_int($val)) {
            $timestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($val);
            return date('Y-m-d', $timestamp);
        }

        return GeneralHelper::lowerTrim($val);
    }

    public function test($object = [])
    {
        $user = Yii::$app->user->identity;
        if ($user->user_id == 1) {
            echo '<pre>';
            print_r($object);
            die;
        }
    }

    public function retBack($url)
    {
        $req = Yii::$app->request;

        if ($req->referrer) {
            return $this->redirect($req->referrer);
        }
        return $this->redirect($url);
    }
}