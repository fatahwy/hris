<?php

namespace app\models\master\search;

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
            [['id_user', 'id_client', 'id_company', 'status', 'id_department', 'id_position', 'basic_salary', 'is_online'], 'integer'],
            [['name', 'email', 'join_date', 'employee_code', 'phone', 'created_at', 'updated_at'], 'safe'],
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
        $query = Account::find();

        // add conditions that should always apply here
        // If we want to filter by client based on session, we can do it in the controller and pass it or handle it here if required.
        // Assuming controller sets the condition via `$searchModel->id_client = ...`

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id_user' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id_user' => $this->id_user,
            'id_client' => $this->id_client,
            'id_company' => $this->id_company,
            'status' => $this->status,
            'id_department' => $this->id_department,
            'id_position' => $this->id_position,
            'basic_salary' => $this->basic_salary,
            'is_online' => $this->is_online,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'employee_code', $this->employee_code])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'join_date', $this->join_date])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
