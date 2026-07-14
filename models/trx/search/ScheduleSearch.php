<?php

namespace app\models\trx\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\trx\Schedule;
use Yii;

/**
 * ScheduleSearch represents the model behind the search form of `app\models\trx\Schedule`.
 */
class ScheduleSearch extends Schedule
{
    public $date_from;
    public $date_to;
    public $checkin_start_from;
    public $checkin_start_to;
    public $workhour_end_from;
    public $workhour_end_to;
    public $checkin_datetime_from;
    public $checkin_datetime_to;
    public $checkout_datetime_from;
    public $checkout_datetime_to;

    public function rules()
    {
        return [
            [['id_schedule', 'id_company', 'id_user', 'id_shift'], 'integer'],
            [['date', 'shift_name', 'checkin_start', 'workhour_start', 'workhour_end', 'status', 'checkin_datetime', 'checkout_datetime'], 'safe'],
            [['date_from', 'date_to', 'checkin_start_from', 'checkin_start_to', 'workhour_end_from', 'workhour_end_to', 'checkin_datetime_from', 'checkin_datetime_to', 'checkout_datetime_from', 'checkout_datetime_to'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function beforeValidate()
    {
        // Parse date range for date
        if (!empty($this->date) && strpos($this->date, ' - ') !== false) {
            $range = explode(' - ', $this->date);
            $this->date_from = $range[0];
            $this->date_to = $range[1] ?? $range[0];
            // $this->date = null;
        }

        // Parse date range for checkin_start
        if (!empty($this->checkin_start) && strpos($this->checkin_start, ' - ') !== false) {
            $range = explode(' - ', $this->checkin_start);
            $this->checkin_start_from = $range[0];
            $this->checkin_start_to = $range[1] ?? $range[0];
            // $this->checkin_start = null;
        }

        // Parse date range for workhour_end
        if (!empty($this->workhour_end) && strpos($this->workhour_end, ' - ') !== false) {
            $range = explode(' - ', $this->workhour_end);
            $this->workhour_end_from = $range[0];
            $this->workhour_end_to = $range[1] ?? $range[0];
            // $this->workhour_end = null;
        }

        // Parse date range for checkin_datetime
        if (!empty($this->checkin_datetime) && strpos($this->checkin_datetime, ' - ') !== false) {
            $range = explode(' - ', $this->checkin_datetime);
            $this->checkin_datetime_from = $range[0];
            $this->checkin_datetime_to = $range[1] ?? $range[0];
            // $this->checkin_datetime = null;
        }

        // Parse date range for checkout_datetime
        if (!empty($this->checkout_datetime) && strpos($this->checkout_datetime, ' - ') !== false) {
            $range = explode(' - ', $this->checkout_datetime);
            $this->checkout_datetime_from = $range[0];
            $this->checkout_datetime_to = $range[1] ?? $range[0];
            // $this->checkout_datetime = null;
        }

        return parent::beforeValidate();
    }

    public function search($params)
    {
        $this->load($params);

        // Only show logged in user's schedule
        $query = Schedule::getQueryByCompany()
            ->joinWith(['user'])
            ->andFilterWhere([
                'id_schedule' => $this->id_schedule,
                'id_shift' => $this->id_shift,
                'schedule.status' => $this->status,
            ])
            ->andFilterWhere(['like', 'shift_name', $this->shift_name]);

        if (is_numeric($this->id_user)) {
            $query->andFilterWhere(['schedule.id_user' => $this->id_user]);
        } else {
            $query->andFilterWhere(['like', 'user.name', $this->id_user]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['date' => SORT_DESC]
            ]
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Date range filtering
        if (!empty($this->date_from) && !empty($this->date_to)) {
            $query->andWhere(['between', 'date', $this->date_from, $this->date_to]);
        } elseif (!empty($this->date_from)) {
            $query->andWhere(['>=', 'date', $this->date_from]);
        } elseif (!empty($this->date_to)) {
            $query->andWhere(['<=', 'date', $this->date_to]);
        }

        // Checkin start datetime range filtering
        if (!empty($this->checkin_start_from) && !empty($this->checkin_start_to)) {
            $query->andWhere(['between', 'checkin_start', $this->checkin_start_from, $this->checkin_start_to]);
        } elseif (!empty($this->checkin_start_from)) {
            $query->andWhere(['>=', 'checkin_start', $this->checkin_start_from]);
        } elseif (!empty($this->checkin_start_to)) {
            $query->andWhere(['<=', 'checkin_start', $this->checkin_start_to]);
        }

        // Workhour end datetime range filtering
        if (!empty($this->workhour_end_from) && !empty($this->workhour_end_to)) {
            $query->andWhere(['between', 'workhour_end', $this->workhour_end_from, $this->workhour_end_to]);
        } elseif (!empty($this->workhour_end_from)) {
            $query->andWhere(['>=', 'workhour_end', $this->workhour_end_from]);
        } elseif (!empty($this->workhour_end_to)) {
            $query->andWhere(['<=', 'workhour_end', $this->workhour_end_to]);
        }

        // Checkin datetime range filtering
        if (!empty($this->checkin_datetime_from) && !empty($this->checkin_datetime_to)) {
            $query->andWhere(['between', 'checkin_datetime', $this->checkin_datetime_from, $this->checkin_datetime_to]);
        } elseif (!empty($this->checkin_datetime_from)) {
            $query->andWhere(['>=', 'checkin_datetime', $this->checkin_datetime_from]);
        } elseif (!empty($this->checkin_datetime_to)) {
            $query->andWhere(['<=', 'checkin_datetime', $this->checkin_datetime_to]);
        }

        // Checkout datetime range filtering
        if (!empty($this->checkout_datetime_from) && !empty($this->checkout_datetime_to)) {
            $query->andWhere(['between', 'checkout_datetime', $this->checkout_datetime_from, $this->checkout_datetime_to]);
        } elseif (!empty($this->checkout_datetime_from)) {
            $query->andWhere(['>=', 'checkout_datetime', $this->checkout_datetime_from]);
        } elseif (!empty($this->checkout_datetime_to)) {
            $query->andWhere(['<=', 'checkout_datetime', $this->checkout_datetime_to]);
        }

        return $dataProvider;
    }
}
