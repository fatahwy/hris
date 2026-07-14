<?php

namespace app\models\report\search;

use app\helpers\GeneralHelper;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\master\Account;
use app\models\master\LeaveType;
use app\models\trx\Schedule;
use app\models\trx\LeaveRequest;
use yii\db\Query;

/**
 * AttendanceSummarySearch represents the model behind the search form for employee attendance summary.
 */
class AttendanceSummarySearch extends Account
{
    public $date_from;
    public $date_to;
    public $status_filter;

    public function rules()
    {
        return [
            [['id_user', 'id_company', 'id_department', 'id_position'], 'integer'],
            [['name', 'email', 'employee_code', 'date_from', 'date_to', 'status_filter'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $this->load($params);

        $id_company = GeneralHelper::session('id_company');

        $qLeaveType = (new Query())
            ->select(['id_leave_type', 'category'])
            ->from(['leave_type'])
            ->where(['id_company' => $id_company])
            ->all();

        $typeCuti = [0];
        $typeIjin = [0];

        foreach ($qLeaveType as $v) {
            if ($v['category'] == LeaveType::CATEGORY_LEAVE) {
                $typeCuti[] = $v['id_leave_type'];
            } else {
                $typeIjin[] = $v['id_leave_type'];
            }
        }

        $subQueryLeaveRequest = (new Query())
            ->select([
                'lr.id_user',
                "COALESCE(SUM(CASE WHEN lr.id_leave_type in (" . implode(',', $typeCuti) . ") THEN lr.total_day ELSE 0 END), 0) as jml_cuti",
                "COALESCE(SUM(CASE WHEN lr.id_leave_type in (" . implode(',', $typeIjin) . ") THEN lr.total_day ELSE 0 END), 0) as jml_ijin",
            ])
            ->from(['lr' => LeaveRequest::tableName()])
            ->where(['lr.status' => LeaveRequest::STATUS_APPROVE])
            ->andWhere(['id_leave_type' => array_merge($typeCuti, $typeIjin)])
            // ->andWhere(['between', 's.date', $this->date_from, $this->date_to])
            // ->andWhere(['between', 'lr.start_date', $this->date_from, $this->date_to])
            ->groupBy(['lr.id_user']);

        // echo '<pre>';
        // print_r(array_merge($typeCuti, $typeIjin));
        // die;

        $query = (new Query())
            ->from(['u' => 'user'])
            ->select([
                'u.id_user',
                'u.uuid',
                'u.name',
                'u.employee_code',
                "SUM(COALESCE(lr.jml_cuti, 0)) as jml_cuti",
                "SUM(COALESCE(lr.jml_ijin, 0)) as jml_ijin",
                'COUNT(DISTINCT s.id_schedule) as jml_shift',
                "COALESCE(SUM(CASE WHEN s.status = '" . Schedule::STATUS_ABSENT . "' THEN 1 ELSE 0 END), 0) as jml_absen",
                "COALESCE(SUM(CASE WHEN s.status IN ('" . Schedule::STATUS_CHECKIN . "', '" . Schedule::STATUS_DONE . "') THEN 1 ELSE 0 END), 0) as jml_kehadiran",
                'COALESCE(SUM(CASE WHEN s.checkin_datetime IS NOT NULL AND s.checkin_datetime <= s.workhour_start THEN 1 ELSE 0 END), 0) as jml_tepat_waktu',
                'COALESCE(SUM(CASE WHEN s.checkin_datetime IS NOT NULL AND s.checkin_datetime > s.workhour_start THEN 1 ELSE 0 END), 0) as jml_keterlambatan',
                'COALESCE(SUM(s.total_workhour), 0) as total_jam_kerja',
            ])
            ->leftJoin(['s' => Schedule::tableName()], 's.id_user = u.id_user')
            ->leftJoin(['lr' => $subQueryLeaveRequest], 'lr.id_user = u.id_user');

        // Apply company filter
        $query->andWhere(['u.id_company' => $id_company]);

        // Filter by name
        $query->andFilterWhere(['like', 'u.name', $this->name]);

        // Date range filtering
        if (!empty($this->date_from) && !empty($this->date_to)) {
            $query->andWhere(['between', 's.date', $this->date_from, $this->date_to]);
            // $query->andWhere(['between', 'lr.start_date', $this->date_from, $this->date_to]);
        } elseif (!empty($this->date_from)) {
            $query->andWhere(['>=', 's.date', $this->date_from]);
            // $query->andWhere(['>=', 'lr.start_date', $this->date_from]);
        } elseif (!empty($this->date_to)) {
            $query->andWhere(['<=', 's.date', $this->date_to]);
            // $query->andWhere(['<=', 'lr.start_date', $this->date_to]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['name' => SORT_ASC],
                'attributes' => [
                    'name' => [
                        'asc' => ['u.name' => SORT_ASC],
                        'desc' => ['u.name' => SORT_DESC],
                    ],
                    'jml_shift' => [
                        'asc' => ['jml_shift' => SORT_ASC],
                        'desc' => ['jml_shift' => SORT_DESC],
                    ],
                    'jml_cuti' => [
                        'asc' => ['jml_cuti' => SORT_ASC],
                        'desc' => ['jml_cuti' => SORT_DESC],
                    ],
                    'jml_ijin' => [
                        'asc' => ['jml_ijin' => SORT_ASC],
                        'desc' => ['jml_ijin' => SORT_DESC],
                    ],
                    'jml_absen' => [
                        'asc' => ['jml_absen' => SORT_ASC],
                        'desc' => ['jml_absen' => SORT_DESC],
                    ],
                    'jml_kehadiran' => [
                        'asc' => ['jml_kehadiran' => SORT_ASC],
                        'desc' => ['jml_kehadiran' => SORT_DESC],
                    ],
                    'total_jam_kerja' => [
                        'asc' => ['total_jam_kerja' => SORT_ASC],
                        'desc' => ['total_jam_kerja' => SORT_DESC],
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Group by user to get aggregated data
        $query->groupBy(['u.id_user', 'u.uuid', 'u.name', 'u.employee_code']);

        return $dataProvider;
    }
}
