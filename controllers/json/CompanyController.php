<?php

namespace app\controllers\json;

use app\controllers\BaseController;
use app\models\master\Company;
use Yii;

class CompanyController extends BaseController
{

    /**
     * Lists all Company models.
     *
     * @return string
     */
    public function actionIndex()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $id = end($_POST['depdrop_parents']);
            $list = Company::find()->andWhere(['id_client' => $this->id_client])->asArray()->all();
            $selected = null;
            if ($id != null && count($list) > 0) {
                $selected = '';
                foreach ($list as $i => $account) {
                    $out[] = ['id' => $account['id_company'], 'name' => $account['name']];
                    if ($i == 0) {
                        $selected = $account['id_company'];
                    }
                }
                // Shows how you can preselect a value
                return ['output' => $out, 'selected' => $selected];
            }
        }

        return ['output' => '', 'selected' => ''];
    }


}