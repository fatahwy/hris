<?php

namespace app\models\trx;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\trx\Schedule;
use Yii;

/**
 * ScheduleSearch represents the model behind the search form of `app\models\trx\Schedule`.
 */
class ScheduleSearch extends Schedule
{
    public function rules()
    {
        return [
            [['id_schedule', 'id_company', 'id_user', 'id_shift'], 'integer'],
            [['date', 'shift_name', 'checkin_start', 'workhour_start', 'workhour_end', 'status', 'checkin_datetime', 'checkout_datetime'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        // Only show logged in user's schedule
        $query = Schedule::find()->where(['id_user' => Yii::$app->user->identity->id_user ?? 0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['date' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id_schedule' => $this->id_schedule,
            'id_company' => $this->id_company,
            'id_shift' => $this->id_shift,
            'date' => $this->date,
            'checkin_datetime' => $this->checkin_datetime,
            'checkout_datetime' => $this->checkout_datetime,
        ]);

        $query->andFilterWhere(['like', 'shift_name', $this->shift_name]);

        $nowStr = \app\helpers\DBHelper::now();
        if ($this->status === 'Belum Checkin') {
            $query->andWhere(['checkin_datetime' => null])
                  ->andWhere(['>=', new \yii\db\Expression('CAST("date" AS VARCHAR) || \' \' || CAST("workhour_end" AS VARCHAR)'), $nowStr]);
        } elseif ($this->status === 'Absent') {
            $query->andWhere(['checkin_datetime' => null])
                  ->andWhere(['<', new \yii\db\Expression('CAST("date" AS VARCHAR) || \' \' || CAST("workhour_end" AS VARCHAR)'), $nowStr]);
        } elseif ($this->status === 'Checkin') {
            $query->andWhere(['not', ['checkin_datetime' => null]])
                  ->andWhere(['checkout_datetime' => null]);
        } elseif ($this->status === 'Selesai') {
            $query->andWhere(['not', ['checkin_datetime' => null]])
                  ->andWhere(['not', ['checkout_datetime' => null]]);
        } elseif (!empty($this->status)) {
            $query->andFilterWhere(['like', 'status', $this->status]);
        }

        return $dataProvider;
    }
}
