<?php

namespace app\controllers\setting;

use app\controllers\BaseController;
use app\helpers\GeneralHelper;
use app\models\master\Company;
use app\models\trx\Schedule;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * GeneralController handles general company settings including payroll deduction rules.
 */
class GeneralController extends BaseController
{
    /**
     * Display and save general settings for the current company.
     *
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        $company = Company::findOne($this->id_company);
        if (!$company) {
            throw new NotFoundHttpException('Perusahaan tidak ditemukan.');
        }

        // Build list of available deduction sources (Salary + Company Allowances)
        $sources = [
            'basic_salary' => 'Gaji Pokok',
        ];

        $allowances = $company->allowance ?? [];
        if (is_array($allowances)) {
            foreach ($allowances as $allowance) {
                if (!empty($allowance['uuid']) && !empty($allowance['name'])) {
                    $sources['allowance_' . $allowance['uuid']] = 'Tunjangan: ' . $allowance['name'];
                }
            }
        }

        // Status list for deduction rules
        $statuses = Schedule::optsStatusPresent();

        if (Yii::$app->request->isPost) {
            $postDeductions = Yii::$app->request->post('deductions', []);
            
            $cleanDeductions = [];
            foreach ($statuses as $statusKey => $statusLabel) {
                $statusData = $postDeductions[$statusKey] ?? [];
                $source = trim($statusData['source'] ?? '');
                $minutes = isset($statusData['minutes']) ? max(0, (int)$statusData['minutes']) : 0;
                $amount = isset($statusData['amount']) ? (float)str_replace(['.', ','], ['', '.'], $statusData['amount']) : 0;

                $cleanDeductions[$statusKey] = [
                    'source' => $source,
                    'minutes' => $minutes,
                    'amount' => $amount,
                ];
            }

            $currentSetting = $company->getSetting();
            $currentSetting['deductions'] = $cleanDeductions;

            if ($company->saveSetting($currentSetting)) {
                GeneralHelper::flashSucceed('Setting pemotongan gaji berhasil disimpan.');
                return $this->redirect(['index']);
            } else {
                GeneralHelper::flashFailed('Gagal menyimpan setting pemotongan gaji.');
            }
        }

        $settingData = $company->getSetting();
        $savedDeductions = $settingData['deductions'] ?? [];

        return $this->render('index', [
            'company' => $company,
            'sources' => $sources,
            'statuses' => $statuses,
            'savedDeductions' => $savedDeductions,
        ]);
    }
}
