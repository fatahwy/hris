<?php

namespace app\components;

use kartik\grid\SerialColumn;
use yii\grid\GridView;

class GroupSerialColumn extends SerialColumn
{
    private $_groupIndex = 1;
    private $_processedGroups = [];

    public function init()
    {
        parent::init();

        $this->grid->on(GridView::EVENT_BEFORE_RUN, function () {
            $this->_groupIndex = 1;
            $this->_processedGroups = [];
        });
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        $groupId = $model['group_id'] ?? null;
        if ($groupId && !isset($this->_processedGroups[$groupId])) {
            $this->_processedGroups[$groupId] = true;
            $page = $this->grid->dataProvider->pagination->getPage();
            $pageSize = $this->grid->dataProvider->pagination->pageSize;
            return ($page * $pageSize) + $this->_groupIndex++;
        }

        if (!$groupId) {
            return parent::renderDataCellContent($model, $key, $index);
        }

        return null;
    }
}
