<?php

namespace app\models;

use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property string $label
 * @property int $type
 * @property string|null $description
 * @property string|null $rule_name
 * @property resource|null $data
 * @property int|null $id_client
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthItemChild[] $authItemChildren
 * @property AuthItemChild[] $authItemChildren0
 * @property AuthItem[] $children
 * @property AuthItem[] $parents
 * @property AuthRule $ruleName
 */
class AuthItem extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'label', 'type'], 'required'],
            [['id_client', 'type', 'created_at', 'updated_at'], 'integer'],
            [['route_menu', 'description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['name'], 'unique'],
            [['name', 'label'], 'filter', 'filter' => 'trim', 'skipOnArray' => true],
            [['rule_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthRule::className(), 'targetAttribute' => ['rule_name' => 'name']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'label' => 'Role',
            'type' => 'Type',
            'description' => 'Description',
            'rule_name' => 'Rule Name',
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[AuthAssignments]].
     *
     * @return ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
    }

    /**
     * Gets query for [[AuthItemChildren]].
     *
     * @return ActiveQuery
     */
    public function getAuthItemChildren()
    {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
    }

    /**
     * Gets query for [[AuthItemChildren0]].
     *
     * @return ActiveQuery
     */
    public function getAuthItemChildren0()
    {
        return $this->hasMany(AuthItemChild::className(), ['child' => 'name']);
    }

    /**
     * Gets query for [[Children]].
     *
     * @return ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'child'])->viaTable('auth_item_child', ['parent' => 'name']);
    }

    /**
     * Gets query for [[Parents]].
     *
     * @return ActiveQuery
     */
    public function getParents()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'parent'])->viaTable('auth_item_child', ['child' => 'name']);
    }

    /**
     * Gets query for [[RuleName]].
     *
     * @return ActiveQuery
     */
    public function getRuleName()
    {
        return $this->hasOne(AuthRule::className(), ['name' => 'rule_name']);
    }

    public static function getQuery()
    {
        $query = AuthItem::find()
            ->where(['type' => 1]);

        if (RoleHelper::isSuper()) {
            $query
                ->andWhere([
                    'OR',
                    ['id_client' => GeneralHelper::session('id_client')],
                    ['LOWER(name)' => ['super', 'owner']]
                ]);

        } else {
            $query->andWhere(['not', ['LOWER(name)' => ['super', 'owner']]])
                ->andWhere(['id_client' => GeneralHelper::session('id_client')]);
        }

        return $query;
    }

    public static function getList($role = null, $clientId = null)
    {
        $clientId = $clientId ?: GeneralHelper::session('id_client');
        $query = self::find()->where(['type' => 1]);

        if (RoleHelper::isSuper()) {
            $query->andWhere([
                'OR',
                ['id_client' => $clientId],
                ['LOWER(name)' => ['super', 'owner']]
            ]);
        } else {
            // Non-super can assign Owner and client-specific roles, but NOT Super
            $query->andWhere(['not', ['LOWER(name)' => 'super']])
                ->andWhere([
                    'OR',
                    ['id_client' => $clientId],
                    ['LOWER(name)' => 'owner']
                ]);
        }

        $models = $query
            ->all();

        $listRole = ArrayHelper::map($models, 'name', 'label');

        if ($role) {
            return !empty($listRole[$role]) ? $listRole[$role] : null;
        }

        return $listRole;
    }

    public static function createRole($id_client, $label)
    {
        $roleName = $label . $id_client;
        $model = self::findOne(['name' => $roleName]);
        if (!$model) {
            $model = new AuthItem();
            $model->label = $label;
            $model->name = $roleName;
            $model->type = 1;
            $model->id_client = $id_client;

            if (!$model->save()) {
                return null;
            }
        }

        return $model;
    }

    public static function createRoleKaryawan($id_client)
    {
        $model = self::createRole($id_client, 'Karyawan');
        if (!$model) {
            return null;
        }

        $exists = AuthItemChild::find()
            ->where(['parent' => $model->name, 'child' => 'dashboard'])
            ->exists();

        if (!$exists) {
            $m = new AuthItemChild();
            $m->parent = $model->name;
            $m->child = 'dashboard';

            if ($m->save()) {
                return $m;
            }
            return null;
        }

        return $model;
    }
}