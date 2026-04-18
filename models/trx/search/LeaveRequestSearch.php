<?php

namespace app\models\trx\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\trx\LeaveRequest;

/**
 * LeaveRequestSearch represents the model behind the search form of `app\models\trx\LeaveRequest`.
 */
class LeaveRequestSearch extends LeaveRequest
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_leave_request', 'id_user', 'id_leave_type', 'total_day', 'id_approver'], 'integer'],
            [['start_date', 'end_date', 'reason', 'attachment', 'status', 'created_at', 'updated_at'], 'safe'],
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
        $query = LeaveRequest::find()
            ->where([
                'OR',
                ['status' => LeaveRequest::STATUS_PENDING],
                ['>', 'approve_at', date('Y-m-d H:i:s', strtotime('-7 days'))],
            ]);

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
            'id_leave_request' => $this->id_leave_request,
            'id_user' => $this->id_user,
            'id_leave_type' => $this->id_leave_type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_day' => $this->total_day,
            'id_approver' => $this->id_approver,
            'DATE(created_at)' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'attachment', $this->attachment])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
