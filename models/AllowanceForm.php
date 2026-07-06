<?php

namespace app\models;

use yii\base\Model;

class AllowanceForm extends Model
{
    public $uuid;
    public $name;
    public $is_fixed;

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['is_fixed'], 'boolean'],
            [['uuid'], 'string', 'max' => 36],
        ];
    }

    public function attributeLabels()
    {
        return [
            'uuid' => 'UUID',
            'name' => 'Nama Tunjangan',
            'is_fixed' => 'Tetap',
        ];
    }
}
