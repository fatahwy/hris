<?php

use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\trx\Schedule $model */
/** @var app\models\master\Account[] $users */

$this->title = 'Form Lembur';
$this->params['breadcrumbs'][] = ['label' => 'Jadwal Kerja', 'url' => ['/trx/schedule']];
$this->params['breadcrumbs'][] = $this->title;

$userList = ArrayHelper::map($users, 'id_user', 'name');
?>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <?php $form = ActiveForm::begin([
            'id' => 'overtime-form',
            'options' => ['class' => 'needs-validation'],
        ]); ?>

        <div class="row mb-3">
            <div class="col-md-12">
                <div class="form-check form-switch">
                    <?= Html::checkbox('is_long_shift', true, [
                        'id' => 'is_long_shift',
                        'class' => 'form-check-input',
                        'checked' => true,
                    ]) ?>
                    <?= Html::label('Hari Libur/Weekend', 'is_long_shift', ['class' => 'form-check-label fw-bold']) ?>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <?= $form->field($model, 'date')->textInput([
                    'type' => 'date',
                    'class' => 'form-control',
                ])->label('Tanggal') ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'id_user')->widget(Select2::class, [
                    'data' => $userList,
                    'options' => ['placeholder' => 'Pilih Pegawai'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label('Pegawai') ?>
            </div>
        </div>

        <!-- Long Shift Fields -->
        <div id="long-shift-fields">
            <div class="row mb-3">
                <div class="col-md-6">
                    <?= $form->field($model, 'id_shift')->dropDownList($shifts) ?>
                </div>
            </div>
        </div>

        <!-- Per Jam Fields -->
        <div id="per-jam-fields" style="display: none;">
            <div class="row mb-3">
                <div class="col-md-6">
                    <?= $form->field($model, 'workhour_start')->textInput([
                        'type' => 'time',
                        'class' => 'form-control',
                    ])->label('Jam Mulai') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'workhour_end')->textInput([
                        'type' => 'number',
                        'step' => '0.5',
                        'min' => '0.5',
                        'class' => 'form-control',
                    ])->label('Estimasi (Jam)') ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 text-end">
                <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary ms-2']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$this->registerJs(<<<JS
jQuery(function($) {
    var isLongShiftCheckbox = $('#is_long_shift');
    var longShiftFields = $('#long-shift-fields');
    var perJamFields = $('#per-jam-fields');
    var overtimeType = $('#overtime-type');

    function toggleFields() {
        if (isLongShiftCheckbox.is(':checked')) {
            longShiftFields.show();
            perJamFields.hide();
            // Clear per jam fields
            $('#schedule-workhour_start').val('');
            $('#schedule-workhour_end').val('');
        } else {
            longShiftFields.hide();
            perJamFields.show();
        }
    }

    isLongShiftCheckbox.on('change', toggleFields);

    // Handle form submission via AJAX
    $('#overtime-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Menyimpan data lembur',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(function() {
                        window.location.href = '/trx/schedule';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat menyimpan data'
                });
            }
        });
    });
});
JS, \yii\web\View::POS_END);
?>