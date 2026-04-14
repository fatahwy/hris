<?php

namespace app\models\mdmsoft;

use yii\db\Query;

class Menu extends \mdm\admin\models\Menu
{

    public function rules()
    {
        return [
            [['name'], 'required'],
            [
                ['parent_name'],
                'in',
                'range' => static::find()->select(['name'])->column(),
                'message' => 'Menu "{value}" not found.'
            ],
            [['parent', 'route', 'data', 'order'], 'default'],
            [
                ['parent'],
                'filterParent',
                'when' => function () {
                    return !$this->isNewRecord;
                }
            ],
            [['order'], 'integer'],
            //            [['route'], 'in',
//                'range' => static::getSavedRoutes(),
//                'message' => 'Route "{value}" not found.']
        ];
    }

    public function attributeLabels()
    {
        return [];
    }

    public static function getMenuSource()
    {
        $tableName = static::tableName();
        $query = (new Query())
            ->select(['m.id', 'm.name', 'm.route', 'm.icon', 'parent_name' => 'p.name'])
            ->from(['m' => $tableName])
            ->leftJoin(['p' => $tableName], '[[m.parent]]=[[p.id]]')
            ->where(['m.stat' => 1])
            ->orderBy('m.order')
            ->cache()
            ->all(static::getDb());

        return $query;
    }

}