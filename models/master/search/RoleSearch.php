<?php

namespace app\models\master\search;

use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\AuthItem;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * RoleSearch represents the model behind the search form of `app\models\AuthItem`.
 */
class RoleSearch extends AuthItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_client'], 'integer'],
            [['label'], 'string'],
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
        $this->load($params, $formName);

        $query = AuthItem::getQuery()
            ->andFilterWhere(['like', 'label', $this->label]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        return $dataProvider;
    }
}