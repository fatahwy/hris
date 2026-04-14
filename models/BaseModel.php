<?php

namespace app\models;


use Yii;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;

class BaseModel extends ActiveRecord
{

    public function setAttributes($values, $safeOnly = true, $exceptFieldName = 'name')
    {
        if (is_array($values)) {
            $attributes = array_flip($safeOnly ? $this->safeAttributes() : $this->attributes());
            foreach ($values as $name => $value) {
                if (isset($attributes[$name])) {
                    if ($value && is_string($value) && $name != $exceptFieldName) {
                        // $value = GeneralHelper::encode($value);
                    }
                    $this->$name = $value;
                } elseif ($safeOnly) {
                    $this->onUnsafeAttribute($name, $value);
                }
            }
        }
    }

    public function loadFilter($data, $formName = null, $exceptFieldName = '')
    {
        $scope = $formName === null ? $this->formName() : $formName;
        if ($scope === '' && !empty($data)) {
            $this->setAttributes($data, true, $exceptFieldName);

            return true;
        } elseif (isset($data[$scope])) {
            $this->setAttributes($data[$scope], true, $exceptFieldName);

            return true;
        }

        return false;
    }

    public static function invalidateCache($tag)
    {
        $dep = new TagDependency(['tags' => $tag]);
        $dep->invalidate(Yii::$app->cache, $tag);
    }
}