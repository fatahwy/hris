<?php


use app\helpers\GeneralHelper;
use app\models\AuthItemChild;
use kartik\select2\Select2;
use kartik\tabs\TabsX;
use richardfan\widget\JSRegister;
use kartik\form\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $model AuthItemChild */
/* @var $form ActiveForm */

$this->title = 'Hak Akses';
$this->params['breadcrumbs'][] = $this->title;

$listRuleAccess = [];
foreach (AuthItemChild::findAll(['parent' => $model->parent]) as $m) {
    $listRuleAccess[preg_replace('/[^A-Za-z0-9\-]/', $replaceRule, $m->child)] = $m->child;
}

JSRegister::begin();
?>
<script>
    $('#cb-role').on('change', function (e) {
        window.location = '<?= Url::to(['/setting/access-rule/index']) ?>?role=' + this.value;
    });

    function cbCheckAll(flag) {
        $('.cb-all input[type="checkbox"]').prop('checked', flag).prop('disabled', flag);
    }

    $('#authitemchild-all_access-child').on('change', function (e) {
        cbCheckAll(this.checked);
    });

    $('.cb-group').on('change', function (e) {
        var selector = $(this).data('val');

        $('.' + selector + ' input[type="checkbox"]').prop('checked', this.checked);
    });

    if (<?= (int) !empty($listRuleAccess['all_access']) ?>) {
        cbCheckAll(true);
    }
</script>
<?php JSRegister::end(); ?>

<!-- <div class="card loader-page">
    <div class="card-body"> -->

<?php
$form = ActiveForm::begin();

echo Html::beginTag('div', ['class' => 'row mb-3']);
echo '<div class="col-md-3">';
echo $form->field($model, 'parent')->widget(Select2::classname(), [
    'data' => $listRole,
    'options' => [
        'id' => 'cb-role',
    ]
])->label('Role');
echo '</div>';
echo Html::endTag('div');

// access menu
$roleList = Html::tag('b', 'Kelola Hak Akses');
$roleList .= Html::beginTag('div', ['class' => 'row']);
foreach ($authItems as $title => $arrAuthItems) {
    $title = trim($title);
    $isSuperAccess = $title == "Super Akses";

    if (!$isSuperAccess) {
        $roleList .= Html::beginTag('div', ['class' => "col-md-12 b cb-all"]);
        $roleList .= Html::tag('label', $title . '&nbsp;' . Html::checkbox(null, null, ['class' => 'cb-group', 'data-val' => "cb-group-$title"]));
        $roleList .= Html::endTag('div');
    }

    foreach ($arrAuthItems as $authItem) {
        $roleList .= Html::beginTag('div', ['class' => 'col-md-3 cb-group-' . $title . ($isSuperAccess ? "" : " cb-all")]);
        $model->child = !empty($listRuleAccess[$authItem->name]);
        $roleList .= $form->field($model, "[{$authItem->name}]child", ['labelOptions' => ['style' => 'font-weight:unset !important']])->checkbox()->label($authItem->description);
        $roleList .= Html::endTag('div');
    }
}
$roleList .= Html::endTag('div');

$items = [
    [
        'label' => 'Menu',
        'content' => $roleList,
        'active' => true
    ],
];

echo TabsX::widget([
    'items' => $items,
    'position' => TabsX::POS_ABOVE,
    'encodeLabels' => false
]);
?>

<div class="form-group text-end">
    <?= Html::submitButton(GeneralHelper::faUpdate(), ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>

<!-- </div>
</div> -->