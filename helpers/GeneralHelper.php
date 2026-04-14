<?php

namespace app\helpers;

use app\models\master\Company;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use Yii;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class GeneralHelper extends \mdm\admin\components\Helper
{

    const STAT_INACTIVE = 0;
    const STAT_ACTIVE = 1;
    const STAT_PENDING = 2;
    const GENDER_MAN = 'L';
    const GENDER_WOMAN = 'P';
    const NON_RECEIPT = 0;
    const RECEIPT = 1;
    const SUBMIT_ACT = 'SUBMIT_ACT';
    const SUBMIT_SAVE = 'SUBMIT_SAVE';

    //    GENERAL HELPER =========

    public static function isGuest()
    {
        return Yii::$app->user ? Yii::$app->user->isGuest : true;
    }

    public static function identity()
    {
        return self::isGuest() ? NULL : Yii::$app->user->identity;
    }

    public static function getBaseUrl($file = NULL)
    {
        return Url::base(true) . '/' . $file;
    }

    public static function getBaseImg($file = NULL)
    {
        return Url::base(true) . '/images/' . $file;
    }

    public static function getBaseFile($file = NULL)
    {
        return Url::base(true) . '/uploads/' . $file;
    }

    public static function session($key, $set = NULL)
    {
        return $set !== null ? Yii::$app->session->set($key, $set) : Yii::$app->session->get($key);
    }

    public static function getFlash($key)
    {
        return Yii::$app->session->getFlash($key);
    }

    public static function setFlash($key, $set)
    {
        return Yii::$app->session->setFlash($key, $set);
    }

    public static function flashSucceed($msg = '')
    {
        return self::setFlash('success', (empty($msg) ? 'Proses berhasil.' : $msg));
    }

    public static function flashFailed($msg = '')
    {
        return self::setFlash('danger', (empty($msg) ? 'Proses gagal!' : $msg));
    }

    public static function encode($string)
    {
        // return mb_convert_encoding(trim($string), 'UTF-8', 'ISO-8859-1');
        return filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    }
    public static function labelChip($title, $severity = 'success')
    {
        return Html::tag('span', $title, ['title' => $title, 'class' => "badge badge-$severity"]);
    }

    public static function filterValue($value)
    {
        if (is_null($value)) {
            return null;
        }
        return strlen($value) === 0 ? null : $value;
    }

    public static function textBloodGroup($stat = null)
    {
        $stats = [
            'A' => 'A',
            'B' => 'B',
            'AB' => 'AB',
            'O' => 'O',
        ];
        return empty($stats[$stat]) ? $stats : $stats[$stat];
    }

    public static function textLabel($text, $stat)
    {
        $stats = [
            self::STAT_INACTIVE => 'danger',
            self::STAT_ACTIVE => 'success',
            self::STAT_PENDING => 'warning',
        ];
        $str = isset($stats[$stat]) ? $stats[$stat] : 'primary';
        return "<span class='badge text-bg-$str'>$text</span>";
    }

    public static function textGender($stat = null)
    {
        $stats = [self::GENDER_MAN => 'L', self::GENDER_WOMAN => 'P'];
        return empty($stats[$stat]) ? $stats : $stats[$stat];
    }

    public static function calcPercent($val, $total, $precision = 0)
    {
        return $total ? round($val * 100 / $total, $precision) : 0;
    }

    public static function getId()
    {
        $key = preg_replace('/[. ]/', '', microtime());
        return $key;
    }

    public static function sumArray($arr, $key)
    {
        $sum = 0;
        foreach ($arr as $row) {
            $sum += is_object($row) ? $row->$key : $row[$key];
        }
        return $sum;
    }

    public static function getDates($i, $year = null)
    {
        $t = date('t', strtotime(($year ?: date('Y')) . "-" . str_pad($i, 2, 0, STR_PAD_LEFT) . "-01"));
        $date = [];
        for ($i = 1; $i <= $t; $i++) {
            $date[$i] = $i;
        }
        return $date;
    }

    public static function getMonths()
    {
        $month = [];
        for ($i = 1; $i <= 12; $i++) {
            $month[$i] = Yii::$app->formatter->asDate("2000-$i-01", 'php:F');
        }
        return $month;
    }

    public static function getYears()
    {
        $year = [];
        for ($i = ((int) date('Y')); $i <= ((int) date('Y')) + 1; $i++) {
            $year[$i] = $i;
        }
        return $year;
    }

    public static function countDay($start_date, $end_date)
    {
        $diff = date_diff(date_create($start_date), date_create($end_date), false);
        return $diff->days;
    }

    public static function diffHour($date, $hour = 48, $hourafter = 48)
    {
        $diff = date_diff(date_create(), date_create($date), false);
        $h = ($diff->y * 8760 + $diff->m * 30 * 24 + $diff->d * 24 + $diff->h + $diff->i / 60) * (1 - ($diff->invert * 2));
        return $h <= $hour && $h > (-1 * $hourafter);
    }

    public static function diffMinutes($date, $minutes = 48, $minutesafter = 48)
    {
        return self::diffHour($date, $minutes / 60, $minutesafter / 60);
    }

    public static function calcAge($dob, $ref = null)
    {
        $now = $ref ? $ref : time();
        return round(($now - strtotime($dob)) / (31557600), 2);
    }

    // CODE GENERATOR
    public static function getRandom($len, $format = 1)
    {
        $sets = ['0956731248', 'STUVWXGHNOPQRYZIJKLMABCDEF', 'ijrstuvwhnopqyzxklmabcdefg'];
        $seeds = [
            $sets[0], // numeric
            $sets[1], // uppercase
            $sets[2], // lowercase
            $sets[0] . $sets[1] . $sets[2], // all
            $sets[0] . $sets[1], //numeric uppercase
            $sets[0] . $sets[2] // numeric lowercase
        ];
        $key = $seeds[$format];
        $keyLen = strlen($key);
        $str = '';
        for ($i = 0; $i < $len; $i++) {
            $str .= $key[rand(0, $keyLen - 1)];
        }
        return $str;
    }

    public static function generateCode($id = NULL)
    {
        $salt = empty($id) ? date('dms') : $id;
        $code = self::getRandom(8 - strlen($salt)) . $salt;
        return $code;
    }

    public static function arrMapLower($model, $key, $value)
    {
        $datas = [];
        foreach ($model as $val) {
            $index = self::lowerTrim($val[$key]);
            $datas[$index] = $val[$value];
        }

        return $datas;
    }

    public static function arrMapUpper($model, $key, $value)
    {
        $datas = [];
        foreach ($model as $val) {
            $index = self::upperTrim($val[$key]);
            $datas[$index] = $val[$value];
        }

        return $datas;
    }

    public static function getInt($str)
    {
        return (int) preg_replace('/\D/', '', $str);
    }

    public static function upperTrim($str)
    {
        return strtoupper(trim($str));
    }

    public static function lowerTrim($str)
    {
        return strtolower(trim($str));
    }

    public static function printr($var, $return = true)
    {
        $dump = '<pre>';
        $dump .= print_r($var, true);
        $dump .= '</pre>';

        if ($return) {
            return $dump;
        } else {
            echo $dump;
        }
    }

    public static function filterActionColumn($buttons = ['view} {update} {delete'], $user = null)
    {
        if (is_array($buttons)) {
            $result = [];
            foreach ($buttons as $button) {
                $result[] = "{{$button}}";
            }
            return implode(' ', $result);
        }
        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($user) {
            return "{{$matches[1]}}";
        }, $buttons);

        if (is_array($buttons)) {
            $result = [];
            foreach ($buttons as $button) {
                if (static::checkRoute($button, [], $user)) {
                    $result[] = "{{$button}}";
                }
            }
            return implode(' ', $result);
        }
        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($user) {
            return static::checkRoute($matches[1], [], $user) ? "{{$matches[1]}}" : '';
        }, $buttons);
    }

    public static function checkValidRoute($route, $html)
    {
        return self::checkRoute($route) ? $html : null;
    }

    public static function getTitleGridview($title = null, $model = null, $colspan = 99)
    {
        $user = self::identity();
        $id_company = $model->id_company ?? $user->id_company;
        $formatter = Yii::$app->formatter;

        $company = Yii::$app->cache->getOrSet("listCompany$id_company", function () use ($id_company) {
            return Company::find()
                ->andWhere(['id_company' => $id_company])
                ->one();
        });
        $options = [
            'class' => 'text-center',
            'style' => 'border: 0px solid;padding:0px',
            'colspan' => $colspan,
        ];

        $header = [
            [
                'columns' => [
                    [
                        'content' => '<h4 class="my-0 font-weight-100">' . $company->name . '</h4>',
                        'tag' => 'td',
                        'options' => $options,
                    ],
                ],
            ],
            $title ? [
                'columns' => [
                    [
                        'content' => "<h4 class='my-0 font-weight-100'>$title</h4>",
                        'tag' => 'td',
                        'options' => $options,
                    ],
                ],
            ] : [],
            !empty($model->date_start) && !empty($model->date_end) ? [
                'columns' => [
                    [
                        'content' => 'Periode ' . $formatter->asDate($model->date_start, 'php:d-m-Y') . ' s.d.' . $formatter->asDate($model->date_end, 'php:d-m-Y'),
                        'tag' => 'td',
                        'options' => $options,
                    ],
                ],
            ] : [],
            [
                'columns' => [
                    [
                        'content' => 'Dibuat Tanggal : <b>' . $formatter->asDate(DBHelper::today(), 'php:d F Y') . '</b>',
                        'tag' => 'td',
                        'options' => $options,
                    ],
                ],
            ]
        ];

        return $header;
    }

    public static function getFormatStruk($id = null)
    {
        $formats = [
            1 => [
                'name' => "Mini (58 mm)",
                'path' => self::getBaseImg('struck/mini.png'),
                'viewFile' => 'thermal',
                'style' => "width: 150px",
            ],
            //    2 => [
            //        'name' => "Medium (10,5 cm)",
            //    ],
            3 => [
                'name' => "Wide (21 cm / A4)",
                'path' => self::getBaseImg('struck/wide-21.png'),
                'viewFile' => 'a4',
                'style' => "width: 300px",
            ],
        ];

        return empty($formats[$id]) ? $formats : $formats[$id];
    }

    public static function setExportList($data)
    {
        if (!empty($data['beforeHeader'])) {
            $data['contentBefore'] = [];

            foreach ($data['beforeHeader'] as $v) {
                if (!empty($v['columns'])) {
                    $label = implode(' ', ArrayHelper::getColumn($v['columns'], 'content'));

                    $data['contentBefore'][] = [
                        'value' => $label,
                    ];
                }
            }
            unset($data['beforeHeader']);
        }

        if (!empty($data['columns'])) {
            foreach ($data['columns'] as $i => $_) {
                if (!empty($data['columns'][$i]['format'])) {
                    $data['columns'][$i]['format'] = 'text';
                }
            }
        }

        return $data;
    }

    public static function cGridExport($dataProvider, $columns, $title, $id = 'selector', $filterModel = null, $toggleAllData = true, $showBtnExport = true)
    {
        $btnExport = ExportMenu::widget(array_merge([
            'filename' => self::slugify($title) . '-' . date('Ymd'),
            'dataProvider' => $dataProvider,
        ], self::setExportList($columns)));

        return GridView::widget(array_merge([
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider,
            'id' => "gridview-id-$id",
            'toolbar' => [
                $showBtnExport ? $btnExport : '',
                $toggleAllData ? '{toggleData}' : '',
            ]
        ], $columns));
    }

    public static function getDueDate()
    {
        $dueDate = date('Y-m-28');

        if (DBHelper::today() >= $dueDate) {
            $dueDate = date('Y-m-28', strtotime('+1 month'));
        }

        return $dueDate;
    }

    public static function cacheFlush()
    {
        Yii::$app->cache->flush();
    }

    public static function faSend($text = 'Kirim')
    {
        return "<i class='bi bi-send'></i> $text";
    }

    public static function faSearch($text = 'Search')
    {
        return "<i class='bi bi-search'></i> $text";
    }

    public static function faAdd($text = 'Tambah')
    {
        return "<i class='bi bi-plus'></i> $text";
    }

    public static function faSave($text = 'Save')
    {
        return "<i class='bi bi-floppy'></i> $text";
    }

    public static function faDownload($text = 'Download')
    {
        return "<i class='bi bi-download'></i> $text";
    }

    public static function faUpdate($text = 'Update')
    {
        return "<i class='bi bi-pencil'></i> $text";
    }

    public static function faRefresh($text = 'Update')
    {
        return "<i class='bi bi-arrow-clockwise'></i> $text";
    }

    public static function faUpload($text = 'Import')
    {
        return "<i class='bi bi-upload'></i> $text";
    }

    public static function faDelete($text = 'Delete')
    {
        return "<i class='bi bi-trash'></i> $text";
    }

    public static function faLogin($text = 'Login')
    {
        return "<i class='bi bi-door-open'></i> $text";
    }

    public static function faLogout($text = 'Logout')
    {
        return "<i class='bi bi-door-closed'></i> $text";
    }

    public static function faList($text = 'List')
    {
        return "<i class='bi bi-list'></i> $text";
    }

    public static function faPrint($text = 'Print')
    {
        return "<i class='bi bi-printer'></i> $text";
    }

    public static function faCompare($text = 'Compare')
    {
        return "<i class='bi bi-arrows'></i> $text";
    }

    public static function saveImgDataUrl($data_url, $path)
    {
        list($type, $data) = explode(';', $data_url);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);

        file_put_contents($path, $data);
    }

    public static function setDelimiter($number, $force = false)
    {
        if ((is_numeric($number) || $force) && $number) {
            $formatter = Yii::$app->formatter;

            return str_contains($number, '.') ? $formatter->asDecimal($number) : $formatter->asInteger($number);
        }

        return $number;
    }

    public static function randColor()
    {
        return 'rgb(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ')'; #using the inbuilt random function 
    }

    public static function processDuration($start_date, $end_date)
    {
        $intMin = strtotime($start_date);
        $intMax = strtotime($end_date);
        $diff = abs($intMax - $intMin);
        $hour = floor($diff / 3600);

        $duration = '< 1 Jam';
        if ($hour >= 24) {
            $duration = floor($hour / 24) . ' Hari';
        } else if ($hour >= 1) {
            $duration = "$hour Jam";
        }

        return $duration;
    }

    public static function addTooltip($desc)
    {
        return Html::tag('i', '', ['class' => "fa fa-info-circle", 'data-toggle' => "tooltip", 'title' => $desc]);
    }

    public static function numberOnly($input)
    {
        if ($input) {
            return preg_replace('/[^0-9]/', '', $input);
        }
        return null;
    }

    public static function formatNumber($input)
    {
        if (filter_var($input, FILTER_VALIDATE_FLOAT) !== false && strpos($input, '.') !== false) {
            return Yii::$app->formatter->asDecimal($input);
        } else {
            return Yii::$app->formatter->asInteger($input);
        }
    }

    public static function getTitle($title, $style = [])
    {
        return Html::tag('h5', $title, array_merge(['class' => 'text-uppercase'], $style));
    }
}