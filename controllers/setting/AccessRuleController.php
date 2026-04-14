<?php

namespace app\controllers\setting;

use app\components\DBHelper;

use app\controllers\BaseController;
use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\AuthItem;
use app\models\AuthItemChild;
use Yii;
use yii\base\Exception;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;

class AccessRuleController extends BaseController
{

    public function actionIndex($role = 'Super')
    {
        $req = Yii::$app->request;
        $query = AuthItem::find()
            ->where(['type' => 1]);

        if (RoleHelper::isSuper()) {
            $query
                ->andWhere([
                    'OR',
                    ['id_client' => $this->id_client],
                    ['LOWER(name)' => ['super', 'owner']]
                ]);

        } else {
            $query->andWhere(['not', ['LOWER(name)' => ['super', 'owner']]])
                ->andWhere(['id_client' => $this->id_client]);
        }
        $listRole = ArrayHelper::map($query->all(), 'name', 'label');

        $model = new AuthItemChild();
        $model->load($req->get());
        $model->parent = $model->parent ?: $role;

        if (empty($listRole[$model->parent])) {
            GeneralHelper::flashFailed('Role tidak ditemukan');
            return $this->redirect(['index']);
        }

        $modelAuthItems = AuthItem::find()
            ->where(['not', ['description' => '']])
            ->andWhere(['type' => 2]) // route
            ->orderBy(['order_val' => SORT_ASC])
            ->all();

        $replaceRule = '_';
        $authItems = $listAuthItems = [];
        foreach ($modelAuthItems as $m) {
            $title = explode('|', $m->description);
            $m->description = $title[1];
            $originalName = $m->name;
            $m->name = preg_replace('/[^A-Za-z0-9\-]/', $replaceRule, $m->name);
            $authItems[$title[0]][] = $m;
            $listAuthItems[$m->name] = $originalName;
        }

        if ($model->load($req->post())) {
            $postAuthItemsChild = $req->post('AuthItemChild');
            $postAuthItemsChild['dashboard'] = ['child' => GeneralHelper::STAT_ACTIVE];
            $listAuthItems['dashboard'] = 'dashboard';

            if (!empty($listRole[$model->parent])) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $flag = AuthItemChild::deleteAll(['parent' => $model->parent]);

                    foreach ($postAuthItemsChild as $child_name => $child_value) {
                        if (!empty($listAuthItems[$child_name]) && !empty($child_value['child'])) {
                            $m = new AuthItemChild();
                            $m->parent = $model->parent;
                            $m->child = $listAuthItems[$child_name];

                            if (($flag = $m->save()) == false) {
                                $transaction->rollBack();
                                GeneralHelper::flashFailed(Html::errorSummary($m));
                                break;
                            }
                        }
                    }
                    // DBHelper::updateBaseRoute($model->parent);

                    if ($flag) {
                        $transaction->commit();
                        GeneralHelper::flashSucceed();
                        GeneralHelper::cacheFlush();
                        return $this->redirect(['index', 'role' => $model->parent]);
                    } else {
                        GeneralHelper::flashFailed($m);
                    }
                } catch (Exception $exc) {
                    GeneralHelper::flashFailed($exc->getTraceAsString());
                }
            }

            return $this->redirect(['index']);
        }

        return $this->render('index', [
            'replaceRule' => $replaceRule,
            'listRole' => $listRole,
            'model' => $model,
            'authItems' => $authItems,
        ]);
    }
}