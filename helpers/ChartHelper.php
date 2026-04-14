<?php

namespace app\helpers;

use yii\bootstrap4\Html;

class ChartHelper
{

    public static function getPeriodCode($field, $code = 'Y-W')
    {
        if ($code == 'Y-W') {
            return "DATE_FORMAT($field,'%Y-%u')";
            // return "CONCAT(YEAR($field), '-', LPAD(WEEK($field),2,0))";
        } else {
            return "DATE_FORMAT($field,'%Y-%m')";
        }
    }

    public static function colors($i = null)
    {
        $lists = [
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 99, 132, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            '#B77474',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)',
            '#FFE5CC',
            '#E5FFCC',
            '#E24027',
            '#CCFFCC',
            '#CCE5FF',
            '#E0E0E0',
            '#92ECD1',
            '#8D7CB5',
            '#AB58AB',
            '#F5F5B3',
            '#B66DD5',
            '#6DD5D2',
        ];

        if (is_numeric($i)) {
            return [$lists[$i]];
        }

        return $lists;
    }

    public static function addCtx($selector = 'myChart', $height = '300px')
    {
        return Html::tag('div', Html::tag('canvas', null, ['style' => "height:$height", 'class' => $selector ?: 'myChart']), ['class' => "mb-5 parent-$selector"]);
    }

    public static function getDataset($label, $data, $type = 'line', $colorIndex = null)
    {
        if ($type == 'line') {
            $dataset = [
                'label' => $label,
                'data' => $data,
                'borderColor' => self::colors($colorIndex),
                'fill' => false,
                'tension' => 0.1,
                'datalabels' => [
                    'align' => 'start',
                    'anchor' => 'start'
                ]
            ];
        } else if ($type == 'pie') {
            $dataset = [
                'label' => $label,
                'data' => $data,
                'backgroundColor' => self::colors($colorIndex),
                'hoverOffset' => 4
            ];
        }

        return $dataset;
    }
}
