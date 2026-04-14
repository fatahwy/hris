<?php

namespace app\controllers;

use app\helpers\RoleHelper;
use app\models\FilterForm;
use app\models\trx\Log;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class LogController extends BaseController
{

    public function actionIndex()
    {
        $user = $this->user;

        $filter = new FilterForm();
        $filter->load($this->request->get());
        $filter->date_start = $filter->date_start ?: date('Y-m-d', strtotime('-10 days'));
        $filter->date_end = $filter->date_end ?: date('Y-m-d');

        $model = new Log();
        $model->load(Yii::$app->request->get());
        $tmp = function ($query) {
            $query->filterWhere(['id_company' => RoleHelper::allCompany() ? NULL : $this->user->id_company]);
        };

        $query = Log::find()
            ->joinWith(['user' => $tmp])
            ->andWhere(['>=', 'date(log.created_at)', $filter->date_start])
            ->andWhere(['<=', 'date(log.created_at)', $filter->date_end])
            ->andFilterWhere(['like', 'date(log.created_at)', $model->created_at])
            ->andFilterWhere(['like', 'user.username', $model->id_user])
            ->andFilterWhere(['like', 'url', $model->url])
            ->andFilterWhere(['like', 'data', $model->data]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC]
            ]
        ]);

        return $this->render('index', [
            'filter' => $filter,
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $isAllBranch = RoleHelper::allCompany();
        $model = $this->findModel($id);

        return $this->render('view', [
            'isAllBranch' => $isAllBranch,
            'model' => $model,
        ]);
    }

    protected function findModel($id)
    {
        $model = Log::find()
            ->innerJoinWith(['user'])
            ->where(['id_log' => $id])
            ->andFilterWhere(['id_company' => RoleHelper::allCompany() ? NULL : $this->user->id_company])
            ->one();

        if ($model) {
            return $model;
        }
        else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}