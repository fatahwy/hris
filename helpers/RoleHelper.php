<?php

namespace app\helpers;

use Yii;

class RoleHelper
{

    public static function can($act)
    {
        return Yii::$app->user->can($act);
    }

    public static function isRole($role)
    {
        $user = GeneralHelper::identity();
        if (empty($user)) {
            return false;
        }
        $roleName = strtolower($user->role->item_name);

        return $role == $roleName;
    }

    public static function isSuper()
    {
        return self::isRole('super');
    }

    public static function allCompany()
    {
        return Yii::$app->user->can('all_company') || self::isSuper();
    }

    public static function allUser()
    {
        return Yii::$app->user->can('all_user') || self::allCompany();
    }

    public static function approvalLeave()
    {
        return Yii::$app->user->can('approval_leave') || self::allCompany();
    }

    public static function approvalPayroll()
    {
        return Yii::$app->user->can('approval_payroll') || self::allCompany();
    }

}