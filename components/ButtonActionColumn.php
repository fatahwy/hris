<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

use app\helpers\GeneralHelper;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use Yii;
use yii\bootstrap5\Html;

class ButtonActionColumn extends ActionColumn
{

    public $dropButtons;

    public function init()
    {
        $this->initColumnSettings([
            'hiddenFromExport' => true,
            'mergeHeader' => false,
            'hAlign' => GridView::ALIGN_LEFT,
            'vAlign' => GridView::ALIGN_MIDDLE,
            'width' => '100px',
        ]);
        $this->_isDropdown = ($this->grid->bootstrap && $this->dropdown);
        if (!isset($this->header)) {
            $this->header = Yii::t('kvgrid', '');
        }
        $this->parseFormat();
        $this->parseVisibility();
        parent::init();
        $this->initDefaultButtons();
        $this->setPageRows();

        //custom
        $this->contentOptions = StyleHelper::buttonActionStyle();
    }

    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'View'),
                    'aria-label' => Yii::t('yii', 'View'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-outline-primary btn-sm',
                    'data-bs-toggle' => 'tooltip',
                    'style' => 'margin: 2px;'
                ], $this->buttonOptions);
                return Html::a(GeneralHelper::faSearch(''), $url, $options);
            };
        }
        if (!isset($this->buttons['create'])) {
            $this->buttons['create'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Tambah'),
                    'aria-label' => Yii::t('yii', 'Tambah'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-outline-success btn-sm',
                    'data-bs-toggle' => 'tooltip',
                    'style' => 'margin: 2px;'
                ], $this->buttonOptions);
                return Html::a(GeneralHelper::faAdd(''), $url, $options);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Update'),
                    'aria-label' => Yii::t('yii', 'Update'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-outline-primary btn-sm',
                    'data-bs-toggle' => 'tooltip',
                    'style' => 'margin: 2px;'
                ], $this->buttonOptions);
                return Html::a(GeneralHelper::faUpdate(''), $url, $options);
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Delete'),
                    'aria-label' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'class' => 'btn btn-outline-danger btn-sm',
                    'data-bs-toggle' => 'tooltip',
                    'style' => 'margin: 2px;'
                ], $this->buttonOptions);
                return Html::a(GeneralHelper::faDelete(''), $url, $options);
            };
        }
        if (!isset($this->buttons['printview'])) {
            $this->buttons['printview'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Cetak'),
                    'aria-label' => Yii::t('yii', 'Cetak'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-outline-success btn-sm',
                    'style' => 'margin: 2px;'
                ], $this->buttonOptions);
                return Html::a(GeneralHelper::faPrint(''), $url, $options);
            };
        }
        if (!isset($this->buttons['print'])) {
            $this->buttons['print'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Cetak'),
                    'aria-label' => Yii::t('yii', 'Cetak'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-outline-default btn-sm',
                    'style' => 'margin: 2px;',
                    'onclick' => "print_report('$url'); return false;",
                ], $this->buttonOptions);
                return Html::a(GeneralHelper::faPrint(''), '#', $options);
            };
        }
        if (!isset($this->buttons['download'])) {
            $this->buttons['download'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Cetak'),
                    'aria-label' => Yii::t('yii', 'Cetak'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-outline-default btn-sm',
                    'style' => 'margin: 2px;',
                    'target' => '_blank',
                ], $this->buttonOptions);
                return Html::a(GeneralHelper::faDownload(''), $url, $options);
            };
        }
        if (!isset($this->buttons['process'])) {
            $this->buttons['process'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Update'),
                    'aria-label' => Yii::t('yii', 'Update'),
                    'data-pjax' => '0',
                    'class' => 'btn btn-outline-primary btn-sm',
                    'data-bs-toggle' => 'tooltip',
                    'style' => 'margin: 2px;'
                ], $this->buttonOptions);
                return Html::a(GeneralHelper::faUpdate(''), $url, $options);
            };
        }
        if (!isset($this->buttons['dropdown'])) {
            $this->buttons['dropdown'] = function ($url, $model, $key) {
                $list = "";
                if (is_array($this->dropButtons) && !empty($this->dropButtons)) {
                    foreach ($this->dropButtons as $i => $row) {
                        $title = is_numeric($i) ? $row : $i;
                        $options = array_merge([
                            'title' => Yii::t('yii', $title),
                            'aria-label' => Yii::t('yii', $title)
                        ], $this->buttonOptions);
                        $list .= Html::tag('li', Html::a($title, str_replace('dropdown', $row, $url), $options));
                    }
                }
                $html = '<div class="btn-group">
                            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <span class="fa fa-cog"> <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right"> ' . $list . ' </ul>
                          </div>';
                return $html;
            };
        }
    }

}