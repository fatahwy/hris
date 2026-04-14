<?php

use app\models\AuthItem;
use app\models\master\Department;
use app\models\master\Position;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\master\Account $model */

$this->title = $model->isNewRecord ? 'Tambah User' : 'Ubah User';
$this->params['breadcrumbs'][] = ['label' => 'Data User', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$controllerIdCompany = $this->context->id_company ?? $this->context->user->id_company;
$departments = ArrayHelper::map(Department::find()->where(['id_company' => $controllerIdCompany])->all(), 'id_department', 'name');
$positions = ArrayHelper::map(Position::find()->where(['id_company' => $controllerIdCompany])->all(), 'id_position', 'name');
?>

<div class="card modern-form-card">
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin(); ?>

        <div class="mb-4 text-muted border-bottom pb-2">
            <i class="fas fa-info-circle me-1"></i> Informasi Dasar
        </div>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Masukkan Nama Lengkap']) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => 'contoh@email.com', 'type' => 'email']) ?>

        <?= $form->field($model, 'password')->passwordInput(['placeholder' => $model->isNewRecord ? 'Masukkan Password' : 'Kosongkan jika tidak ingin mengubah password']) ?>

        <?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'placeholder' => 'No. Telepon / WhatsApp (08xxxx)']) ?>

        <?=
            $form->field($modelAuthAssignment, 'item_name')->widget(Select2::classname(), [
                'data' => AuthItem::getList(),
            ])
            ?>

        <div class="mb-4 text-muted border-bottom pb-2 mt-5">
            <i class="fas fa-briefcase me-1"></i> Informasi Pekerjaan
        </div>

        <?= $form->field($model, 'employee_code')->textInput(['maxlength' => true, 'placeholder' => 'NIK/Kode Karyawan']) ?>

        <?= $form->field($model, 'id_department')->dropDownList($departments, ['prompt' => '- Pilih Departemen -']) ?>

        <?= $form->field($model, 'id_position')->dropDownList($positions, ['prompt' => '- Pilih Jabatan -']) ?>

        <?= $form->field($model, 'basic_salary')->textInput(['type' => 'number', 'placeholder' => 'Gaji Pokok (Angka saja)']) ?>

        <?= $form->field($model, 'join_date')->textInput(['type' => 'date']) ?>

        <?= $form->field($model, 'status')->dropDownList([1 => 'Active', 0 => 'Inactive'], ['class' => 'form-select w-50']) ?>

        <div class="form-group text-end mt-5 pt-3 border-top">
            <?= Html::a('<i class="fas fa-times me-1"></i> Batal', ['index'], ['class' => 'btn btn-light px-4 me-2 border']) ?>
            <?= Html::submitButton('<i class="fas fa-save me-1"></i> Simpan', ['class' => 'btn btn-primary px-5 shadow-sm']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>