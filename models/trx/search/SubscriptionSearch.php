<?php

namespace app\models\trx\search;

use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\trx\Subscription;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SubscriptionSearch represents the model behind the search form of `app\models\trx\Subscription`.
 */
class SubscriptionSearch extends Subscription
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_subscription', 'id_client', 'id_company', 'id_paket', 'durasi', 'jumlah_bulan', 'status'], 'integer'],
            [['price'], 'number'],
            [['created_at', 'updated_at', 'approved_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Subscription::find()
            ->joinWith(['company', 'paket']);

        if (!RoleHelper::isSuper()) {
            $query->andWhere(['subscription.id_company' => GeneralHelper::session('id_company')]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id_subscription' => SORT_DESC],
            ],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'subscription.id_subscription' => $this->id_subscription,
            'subscription.id_client' => $this->id_client,
            'subscription.id_company' => $this->id_company,
            'subscription.id_paket' => $this->id_paket,
            'subscription.jumlah_bulan' => $this->jumlah_bulan,
            'subscription.status' => $this->status,
            'DATE(subscription.created_at)' => $this->created_at,
        ]);

        return $dataProvider;
    }
}
