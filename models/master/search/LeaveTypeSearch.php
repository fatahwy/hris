<?php

namespace app\models\master\search;

use app\helpers\GeneralHelper;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\master\LeaveType;

/**
 * LeaveTypeSearch represents the model behind the search form of `app\models\master\LeaveType`.
 */
class LeaveTypeSearch extends LeaveType
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_leave_type', 'id_company', 'max_day', 'id_client'], 'integer'],
            [['name', 'category', 'created_at', 'updated_at'], 'safe'],
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
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = LeaveType::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id_leave_type' => $this->id_leave_type,
            'id_company' => GeneralHelper::session('id_company'),
            'max_day' => $this->max_day,
            'DATE(created_at)' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'category', $this->category]);

        return $dataProvider;
    }
}