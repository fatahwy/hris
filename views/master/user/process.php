<?php

use app\models\AuthItem;
use app\models\master\Account;
use app\models\master\Department;
use app\models\master\Position;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use kartik\number\NumberControl;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\master\Account $model */
/** @var app\models\AuthAssignment $modelAuthAssignment */
/** @var array $companyAllowances */

$currentPath = Yii::$app->request->pathInfo;
$isProfile = in_array($currentPath, ['profile', 'profile/index', 'master/user/view']);

if ($isProfile) {
    $this->title = $title ?? "Profile";
    $this->registerCss("
        .profile .form-control:disabled {
            background-color: inherit !important;
            border: none !important;
        }
    ");
} else {
    $this->title = $model->isNewRecord ? 'Tambah User' : 'Ubah User';
    $this->params['breadcrumbs'][] = ['label' => 'Data User', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
}

$controllerIdCompany = $this->context->id_company ?? $this->context->user->id_company;
$departments = ArrayHelper::map(Department::find()->where(['id_company' => $controllerIdCompany])->all(), 'id_department', 'name');
$positions = ArrayHelper::map(Position::find()->where(['id_company' => $controllerIdCompany])->all(), 'id_position', 'name');
?>

<div class="card modern-form-card profile">
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin(); ?>

        <div class="mb-4 text-muted border-bottom pb-2">
            <i class="fas fa-info-circle me-1"></i> Informasi Dasar
        </div>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => $isProfile ? '-' : 'Masukkan Nama Lengkap', 'disabled' => $isProfile]) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => $isProfile ? '-' : 'contoh@email.com', 'type' => 'email', 'disabled' => $isProfile]) ?>

        <?= $isProfile ? '' : $form->field($model, 'password')->passwordInput(['placeholder' => $model->isNewRecord ? 'Masukkan Password' : 'Kosongkan jika tidak ingin mengubah password']) ?>

        <?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'placeholder' => $isProfile ? '-' : 'No. Telepon / WhatsApp (08xxxx)', 'disabled' => $isProfile]) ?>

        <?=
            $isProfile ?
            $form->field($model, 'role')->textInput(['value' => $model->role->item_name, 'disabled' => true])
            : $form->field($modelAuthAssignment, 'item_name')->widget(Select2::classname(), [
                'data' => AuthItem::getList(),
            ])
            ?>

        <div class="mb-4 text-muted border-bottom pb-2 mt-5">
            <i class="fas fa-briefcase me-1"></i> Informasi Pekerjaan
        </div>

        <?= $form->field($model, 'employee_code')->textInput(['maxlength' => true, 'placeholder' => $isProfile ? '-' : 'NIK/Kode Karyawan', 'disabled' => $isProfile]) ?>

        <?= $form->field($model, 'id_department')->dropDownList($departments, ['prompt' => $isProfile ? '-' : '- Pilih Departemen -', 'disabled' => $isProfile]) ?>

        <?= $form->field($model, 'id_position')->dropDownList($positions, ['prompt' => $isProfile ? '-' : '- Pilih Jabatan -', 'disabled' => $isProfile]) ?>

        <?= $form->field($model, 'join_date')->textInput(['placeholder' => $isProfile ? '-' : 'Tanggal Bergabung', 'type' => $isProfile ? 'text' : 'date', 'disabled' => $isProfile]) ?>

        <?= $form->field($model, 'status')->dropDownList([1 => 'Active', 0 => 'Inactive'], ['disabled' => $isProfile]) ?>

        <?= $form->field($model, 'basic_salary')->widget(NumberControl::classname(), [
            'maskedInputOptions' => [
                'prefix' => '',
                'suffix' => '',
                'allowNegative' => false,
                'groupSeparator' => '.',
                'radixPoint' => ',',
                'digits' => 0,
                'rightAlign' => false,
            ],
            'displayOptions' => [
                'placeholder' => $isProfile ? '-' : 'Gaji Pokok',
                'disabled' => $isProfile,
            ],
        ]) ?>

        <?=
            $isProfile ?
            $form->field($model, 'ptkp')->textInput(['disabled' => true])
            : $form->field($model, 'ptkp')->widget(Select2::classname(), [
                'data' => Account::listPtkp(true),
                'options' => ['placeholder' => $isProfile ? '-' : '- Pilih PTKP -'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ])
            ?>

        <?php
        if (!empty($companyAllowances)):
            foreach ($companyAllowances as $allowance):
                echo $form->field($model, 'allowance_items[' . $allowance['uuid'] . ']')->widget(NumberControl::classname(), [
                    'maskedInputOptions' => [
                        'prefix' => '',
                        'suffix' => '',
                        'allowNegative' => false,
                        'groupSeparator' => '.',
                        'radixPoint' => ',',
                        'digits' => 0,
                        'rightAlign' => false,
                    ],
                    'displayOptions' => [
                        'placeholder' => $isProfile ? '-' : $allowance['name'],
                        'disabled' => $isProfile,
                    ],
                ])->label($allowance['name']);
            endforeach;
        endif;
        ?>

        <?php if (!$isProfile): ?>
            <div class="form-group text-end mt-5 pt-3 border-top">
                <?= Html::a('<i class="fas fa-times me-1"></i> Batal', ['index'], ['class' => 'btn btn-light px-4 me-2 border']) ?>
                <?= Html::submitButton('<i class="fas fa-save me-1"></i> Simpan', ['class' => 'btn btn-primary px-5 shadow-sm']) ?>
            </div>
        <?php endif; ?>

        <?php ActiveForm::end(); ?>

    </div>
</div>