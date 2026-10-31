<?php

use app\helpers\GeneralHelper;
use app\models\AuthItemChild;
use kartik\select2\Select2;
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

<div class="setting-access-rule-index">
    <div class="card modern-form-card shadow-sm border-0">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-shield-check text-primary"></i> Kelola Hak Akses (Role Permissions)
            </h5>
            <span class="text-muted small">Tentukan hak izin akses menu dan fungsionalitas sistem untuk setiap role</span>
        </div>
        <div class="card-body p-4">
            <?php $form = ActiveForm::begin(); ?>

            <!-- Role Selector Bar -->
            <div class="card bg-light border-0 rounded-3 p-3 mb-4">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 col-lg-4">
                        <label class="form-label fw-bold text-dark small mb-1">
                            <i class="bi bi-person-badge text-primary me-1"></i> Pilih Role untuk Dikelola:
                        </label>
                        <?= $form->field($model, 'parent', ['options' => ['class' => 'm-0']])->widget(Select2::classname(), [
                            'data' => $listRole,
                            'options' => [
                                'id' => 'cb-role',
                            ],
                            'pluginOptions' => [
                                'allowClear' => false,
                                'width' => '100%',
                            ],
                        ])->label(false); ?>
                    </div>
                    <div class="col-12 col-md-6 col-lg-8 mt-2 mt-md-0">
                        <div class="small text-muted ps-md-3">
                            <i class="bi bi-info-circle me-1 text-primary"></i> Memilih role akan otomatis memuat konfigurasi hak akses yang sedang aktif untuk role tersebut.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permission Groups -->
            <div class="d-flex flex-column gap-4">
                <?php foreach ($authItems as $title => $arrAuthItems): 
                    $title = trim($title);
                    $isSuperAccess = ($title == "Super Akses");
                ?>
                    <div class="card border rounded-3 overflow-hidden shadow-none bg-white">
                        <div class="card-header bg-light py-2.5 px-3 d-flex align-items-center justify-content-between border-bottom">
                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-folder2-open text-primary"></i> <?= Html::encode($title) ?>
                            </div>
                            <?php if (!$isSuperAccess): ?>
                                <div class="form-check form-switch m-0 cb-all">
                                    <input type="checkbox" class="form-check-input cb-group" id="cb-group-<?= Html::encode($title) ?>" data-val="cb-group-<?= Html::encode($title) ?>">
                                    <label class="form-check-label small fw-semibold text-muted" for="cb-group-<?= Html::encode($title) ?>">Pilih Semua</label>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-2">
                                <?php foreach ($arrAuthItems as $authItem): ?>
                                    <div class="col-12 col-sm-6 col-lg-3 cb-group-<?= Html::encode($title) ?> <?= $isSuperAccess ? "" : "cb-all" ?>">
                                        <div class="p-2.5 rounded-3 border bg-light h-100 d-flex align-items-center">
                                            <?php
                                            $model->child = !empty($listRuleAccess[$authItem->name]);
                                            echo $form->field($model, "[{$authItem->name}]child", [
                                                'options' => ['class' => 'm-0 w-100'],
                                                'labelOptions' => ['class' => 'small fw-medium text-dark m-0 d-block cursor-pointer']
                                            ])->checkbox([
                                                'class' => 'form-check-input me-2',
                                                'label' => Html::encode($authItem->description ?: $authItem->name),
                                                'custom' => true,
                                            ]);
                                            ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="form-group d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 mt-4 pt-3 border-top">
                <?= Html::submitButton(GeneralHelper::faUpdate('Simpan Perubahan Hak Akses'), ['class' => 'btn btn-primary px-4 shadow-sm w-100 w-sm-auto text-center']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
