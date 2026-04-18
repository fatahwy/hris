<?php

namespace app\models\master\search;

use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\master\Company;

/**
 * CompanySearch represents the model behind the search form of `app\models\master\Company`.
 */
class CompanySearch extends Company
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_company', 'id_client', 'status', 'max_user'], 'integer'],
            [['name', 'address', 'created_at', 'updated_at'], 'safe'],
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
        $query = Company::find()
            ->andWhere(['id_client' => GeneralHelper::session('id_client')])
            ->andWhere(['id_company' => GeneralHelper::session('id_company')]);

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
            'status' => $this->status,
            'max_user' => $this->max_user,
            'DATE(created_at)' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address', $this->address]);

        return $dataProvider;
    }
}