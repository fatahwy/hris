<?php

namespace app\controllers\master;

use app\controllers\BaseController;
use app\helpers\GeneralHelper;
use app\models\AllowanceForm;
use app\models\master\Company;
use Ramsey\Uuid\Uuid;
use yii\bootstrap5\Html;
use yii\web\NotFoundHttpException;

/**
 * AllowanceController manages allowance data stored in Company model
 */
class AllowanceController extends BaseController
{

    /**
     * Lists all allowances for the current company.
     *
     * @return string
     */
    public function actionIndex()
    {
        $company = $this->findCompany();
        $allowances = $company->allowance ?? [];

        return $this->render('index', [
            'company' => $company,
            'allowances' => $allowances,
        ]);
    }

    public function actionProcess($id = null)
    {
        $company = $this->findCompany();
        $allowances = $company->allowance ?? [];

        $model = new AllowanceForm();

        if ($id !== null) {
            $allowance = $this->findAllowance($allowances, $id);
            if ($allowance) {
                $model->uuid = $allowance['uuid'];
                $model->name = $allowance['name'];
                $model->is_fixed = $allowance['is_fixed'];
            }
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->validate()) {
                $newAllowance = [
                    'uuid' => $model->uuid ?: Uuid::uuid4()->toString(),
                    'name' => $model->name,
                    'is_fixed' => $model->is_fixed,
                ];

                if ($id !== null) {
                    $allowances = $this->updateAllowance($allowances, $id, $newAllowance);
                } else {
                    $allowances[] = $newAllowance;
                }

                $company->allowance = $allowances;
                if ($company->save()) {
                    GeneralHelper::flashSucceed();
                    return $this->redirect(['index']);
                }
                GeneralHelper::flashFailed(Html::errorSummary($company));
            }
        }

        return $this->render('process', [
            'company' => $company,
            'model' => $model,
            'id' => $id,
        ]);
    }

    public function actionDelete($id)
    {
        $company = $this->findCompany();
        $allowances = $company->allowance ?? [];

        $allowances = $this->deleteAllowance($allowances, $id);
        $company->allowance = $allowances;
        $company->save();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Company model based on the current user's company.
     * @return Company the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCompany()
    {
        if (($model = Company::findOne($this->id_company)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Find allowance by UUID
     * @param array $allowances
     * @param string $uuid
     * @return array|null
     */
    protected function findAllowance($allowances, $uuid)
    {
        foreach ($allowances as $allowance) {
            if (isset($allowance['uuid']) && $allowance['uuid'] === $uuid) {
                return $allowance;
            }
        }
        return null;
    }

    /**
     * Update allowance by UUID
     * @param array $allowances
     * @param string $uuid
     * @param array $newData
     * @return array
     */
    protected function updateAllowance($allowances, $uuid, $newData)
    {
        foreach ($allowances as $key => $allowance) {
            if (isset($allowance['uuid']) && $allowance['uuid'] === $uuid) {
                $allowances[$key] = $newData;
                break;
            }
        }
        return $allowances;
    }

    /**
     * Delete allowance by UUID
     * @param array $allowances
     * @param string $uuid
     * @return array
     */
    protected function deleteAllowance($allowances, $uuid)
    {
        foreach ($allowances as $key => $allowance) {
            if (isset($allowance['uuid']) && $allowance['uuid'] === $uuid) {
                unset($allowances[$key]);
                break;
            }
        }
        return array_values($allowances);
    }
}
