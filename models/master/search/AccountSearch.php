<?php

namespace app\models\master\search;

use app\helpers\RoleHelper;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\master\Account;

/**
 * AccountSearch represents the model behind the search form of `app\models\master\Account`.
 */
class AccountSearch extends Account
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'id_client', 'id_company', 'status', 'id_department', 'id_position', 'id_manager', 'basic_salary', 'is_online'], 'integer'],
            [['name', 'email', 'join_date', 'employee_code', 'phone', 'nik', 'employment_status', 'work_location', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::getQueryByCompany('user')
            ->with(['department', 'position', 'manager']);

        if (!RoleHelper::isSuper()) {
            $query->innerJoinWith(['role'])
                ->andWhere(['!=', 'LOWER(item_name)', 'super']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id_user' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id_user' => $this->id_user,
            'status' => $this->status,
            'id_department' => $this->id_department,
            'id_position' => $this->id_position,
            'id_manager' => $this->id_manager,
            'employment_status' => $this->employment_status,
            'basic_salary' => $this->basic_salary,
            'is_online' => $this->is_online,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'employee_code', $this->employee_code])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'nik', $this->nik])
            ->andFilterWhere(['like', 'work_location', $this->work_location])
            ->andFilterWhere(['like', 'join_date', $this->join_date])
            ->andFilterWhere(['like', 'date(user.created_at)', $this->created_at])
            ->andFilterWhere(['like', 'date(user.updated_at)', $this->updated_at]);

        return $dataProvider;
    }
}
