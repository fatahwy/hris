<?php

namespace app\models\trx\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\trx\Payroll;

/**
 * PayrollSearch represents the model behind the search form of `app\models\trx\Payroll`.
 */
class PayrollSearch extends Payroll
{
    public $period_start_from;
    public $period_start_to;
    public $period_end_from;
    public $period_end_to;
    public $created_at_from;
    public $created_at_to;

    public function rules()
    {
        return [
            [['id_payroll', 'id_company', 'id_user', 'basic_salary', 'overtime', 'dedection', 'tax', 'gross_salary', 'net_salary', 'id_user_generate', 'id_user_verify', 'id_user_approve'], 'integer'],
            [['period_start', 'period_end', 'status', 'user_verify_at', 'user_approve_at', 'created_at', 'updated_at'], 'safe'],
            [['allowance'], 'safe'],
            [['period_start_from', 'period_start_to', 'period_end_from', 'period_end_to', 'created_at_from', 'created_at_to'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function beforeValidate()
    {
        // Parse date range for period_start
        if (!empty($this->period_start) && strpos($this->period_start, ' - ') !== false) {
            $range = explode(' - ', $this->period_start);
            $this->period_start_from = $range[0];
            $this->period_start_to = $range[1] ?? $range[0];
            // $this->period_start = null;
        }

        // Parse date range for period_end
        if (!empty($this->period_end) && strpos($this->period_end, ' - ') !== false) {
            $range = explode(' - ', $this->period_end);
            $this->period_end_from = $range[0];
            $this->period_end_to = $range[1] ?? $range[0];
            // $this->period_end = null;
        }

        // Parse date range for created_at
        if (!empty($this->created_at) && strpos($this->created_at, ' - ') !== false) {
            $range = explode(' - ', $this->created_at);
            $this->created_at_from = $range[0];
            $this->created_at_to = $range[1] ?? $range[0];
            // $this->created_at = null;
        }

        return parent::beforeValidate();
    }

    public function search($params)
    {
        $this->load($params);

        $query = Payroll::find()
            ->andWhere(['id_user' => $this->id_user, 'status' => Payroll::STATUS_APPROVE])
            ->andFilterWhere([
                'id_payroll' => $this->id_payroll,
                'id_company' => $this->id_company,
                'basic_salary' => $this->basic_salary,
                'overtime' => $this->overtime,
                'dedection' => $this->dedection,
                'tax' => $this->tax,
                'gross_salary' => $this->gross_salary,
                'net_salary' => $this->net_salary,
            ])
            ->andFilterWhere(['like', 'status', $this->status]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['period_start' => SORT_DESC]
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // Period start date range filtering
        if (!empty($this->period_start_from) && !empty($this->period_start_to)) {
            $query->andWhere(['between', 'period_start', $this->period_start_from, $this->period_start_to]);
        } elseif (!empty($this->period_start_from)) {
            $query->andWhere(['>=', 'period_start', $this->period_start_from]);
        } elseif (!empty($this->period_start_to)) {
            $query->andWhere(['<=', 'period_start', $this->period_start_to]);
        }

        // Period end date range filtering
        if (!empty($this->period_end_from) && !empty($this->period_end_to)) {
            $query->andWhere(['between', 'period_end', $this->period_end_from, $this->period_end_to]);
        } elseif (!empty($this->period_end_from)) {
            $query->andWhere(['>=', 'period_end', $this->period_end_from]);
        } elseif (!empty($this->period_end_to)) {
            $query->andWhere(['<=', 'period_end', $this->period_end_to]);
        }

        // created at date range filtering
        if (!empty($this->created_at_from) && !empty($this->created_at_to)) {
            $query->andWhere(['between', 'created_at', $this->created_at_from, $this->created_at_to]);
        } elseif (!empty($this->created_at_from)) {
            $query->andWhere(['>=', 'created_at', $this->created_at_from]);
        } elseif (!empty($this->created_at_to)) {
            $query->andWhere(['<=', 'created_at', $this->created_at_to]);
        }

        return $dataProvider;
    }
}
