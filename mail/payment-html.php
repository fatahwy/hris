<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\master\Client $client */
/** @var app\models\master\Paket|null $paket */
/** @var string $adminPhone */
/** @var string|null $bankName */
/** @var string|null $bankAccountNumber */
/** @var string|null $bankAccountName */

$price = $paket ? (float)$paket->price : 0;
$formattedPrice = 'Rp ' . number_format($price, 0, ',', '.');
$packageName = $paket ? $paket->nama_paket : 'Langganan HRIS';
$packageDuration = $paket ? $paket->durasi_hari . ' Hari (' . $paket->durasi_satuan . ')' : '-';
$maxUser = $paket ? $paket->max_user . ' User' : '-';

$bankName = $bankName ?? \app\helpers\GeneralHelper::BANK_NAME;
$bankAccountNumber = $bankAccountNumber ?? \app\helpers\GeneralHelper::BANK_ACCOUNT_NUMBER;
$bankAccountName = $bankAccountName ?? \app\helpers\GeneralHelper::BANK_ACCOUNT_NAME;

// Format nomor WhatsApp untuk URL wa.me (mengubah awalan 0 menjadi 62)
$waClean = preg_replace('/[^0-9]/', '', $adminPhone);
if (strpos($waClean, '0') === 0) {
    $waClean = '62' . substr($waClean, 1);
}

$waMessage = "Halo Admin " . Yii::$app->name . ",\n\n"
    . "Saya ingin mengirimkan bukti pembayaran langganan:\n"
    . "- Instansi: " . $client->name . "\n"
    . "- Email: " . $client->email . "\n"
    . "- Paket: " . $packageName . "\n"
    . "- Total: " . $formattedPrice . "\n"
    . "- Bank Tujuan: " . $bankName . " (" . $bankAccountNumber . " a/n " . $bankAccountName . ")\n\n"
    . "Mohon untuk diverifikasi dan dilakukan aktivasi akun perusahaan. Terima kasih.";

$waUrl = 'https://wa.me/' . $waClean . '?text=' . rawurlencode($waMessage);
?>

<div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 28px; color: #374151; line-height: 1.6;">
    <div style="text-align: center; margin-bottom: 24px; border-bottom: 1px solid #f3f4f6; padding-bottom: 16px;">
        <h2 style="color: #1f2937; margin: 0 0 6px 0; font-size: 22px;"><?= Html::encode(Yii::$app->name) ?></h2>
        <p style="color: #6b7280; margin: 0; font-size: 14px;">Instruksi Pembayaran & Aktivasi Akun</p>
    </div>

    <p style="font-size: 15px; margin-top: 0;">Halo <strong><?= Html::encode($client->name) ?></strong>,</p>

    <p style="font-size: 14px; margin-bottom: 20px;">
        Terima kasih telah melakukan pendaftaran di <strong><?= Html::encode(Yii::$app->name) ?></strong>. 
        Untuk melanjutkan proses aktivasi akun perusahaan Anda, silakan lakukan pembayaran senilai paket langganan yang Anda pilih:
    </p>

    <!-- Detail Paket & Pembayaran -->
    <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 18px; margin-bottom: 20px;">
        <h3 style="margin-top: 0; margin-bottom: 14px; font-size: 16px; color: #111827; border-bottom: 1px dashed #d1d5db; padding-bottom: 8px;">
            Rincian Pesanan Paket
        </h3>
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <tr>
                <td style="padding: 6px 0; color: #6b7280; width: 45%;">Nama Instansi</td>
                <td style="padding: 6px 0; font-weight: 600; color: #111827;"><?= Html::encode($client->name) ?></td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #6b7280;">Email Terdaftar</td>
                <td style="padding: 6px 0; color: #111827;"><?= Html::encode($client->email) ?></td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #6b7280;">Paket Pilihan</td>
                <td style="padding: 6px 0; font-weight: 600; color: #111827;"><?= Html::encode($packageName) ?></td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #6b7280;">Durasi Langganan</td>
                <td style="padding: 6px 0; color: #111827;"><?= Html::encode($packageDuration) ?></td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #6b7280;">Kapasitas Pengguna</td>
                <td style="padding: 6px 0; color: #111827;"><?= Html::encode($maxUser) ?></td>
            </tr>
            <tr style="border-top: 1px solid #e5e7eb;">
                <td style="padding: 12px 0 6px 0; color: #374151; font-weight: 600; font-size: 15px;">Total Pembayaran</td>
                <td style="padding: 12px 0 6px 0; font-weight: 700; color: #16a34a; font-size: 18px;"><?= $formattedPrice ?></td>
            </tr>
        </table>
    </div>

    <!-- Informasi Rekening Pembayaran Bank -->
    <div style="background-color: #f0f7ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 18px; margin-bottom: 20px;">
        <h3 style="margin-top: 0; margin-bottom: 14px; font-size: 16px; color: #0369a1; border-bottom: 1px dashed #7dd3fc; padding-bottom: 8px;">
            Rekening Tujuan Pembayaran
        </h3>
        <p style="margin: 0 0 12px 0; font-size: 13px; color: #0284c7;">
            Silakan melakukan transfer senilai <strong><?= $formattedPrice ?></strong> ke rekening resmi berikut:
        </p>
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <tr>
                <td style="padding: 6px 0; color: #64748b; width: 45%;">Bank Tujuan</td>
                <td style="padding: 6px 0; font-weight: 700; color: #0f172a; font-size: 15px;"><?= Html::encode($bankName) ?></td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #64748b;">Nomor Rekening</td>
                <td style="padding: 6px 0; font-weight: 700; color: #0369a1; font-size: 17px; letter-spacing: 0.5px;">
                    <?= Html::encode($bankAccountNumber) ?>
                </td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #64748b;">Atas Nama (A/N)</td>
                <td style="padding: 6px 0; font-weight: 600; color: #0f172a;"><?= Html::encode($bankAccountName) ?></td>
            </tr>
        </table>
    </div>

    <!-- Instruksi Konfirmasi Bukti Bayar via WA -->
    <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 18px; margin-bottom: 24px; text-align: center;">
        <h4 style="margin: 0 0 10px 0; color: #065f46; font-size: 15px;">
            Konfirmasi Bukti Pembayaran
        </h4>
        <p style="margin: 0 0 16px 0; font-size: 14px; color: #047857; line-height: 1.5;">
            Setelah melakukan transfer pembayaran senilai <strong><?= $formattedPrice ?></strong>, 
            mohon kirimkan foto/tangkapan layar bukti transfer melalui WhatsApp ke nomor admin kami:
            <br>
            <strong style="font-size: 16px; color: #064e3b; letter-spacing: 0.5px;"><?= Html::encode($adminPhone) ?></strong>
        </p>

        <a href="<?= Html::encode($waUrl) ?>" 
           target="_blank" 
           style="display: inline-block; background-color: #25D366; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 14px; padding: 12px 24px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            Kirim Bukti Bayar via WhatsApp
        </a>
    </div>

    <p style="font-size: 13px; color: #6b7280; margin-bottom: 12px;">
        <strong>Catatan:</strong> Akun perusahaan Anda akan segera diaktifkan oleh admin setelah bukti transfer diverifikasi.
    </p>

    <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #f3f4f6; font-size: 12px; color: #9ca3af; text-align: center;">
        &copy; <?= date('Y') ?> <?= Html::encode(Yii::$app->name) ?>. Semua hak dilindungi.
    </div>
</div>
