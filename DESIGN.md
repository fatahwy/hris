# Design System & UI Guidelines (DESIGN.md)

Dokumen ini berfungsi sebagai panduan referensi utama bagi AI Agent dan developer dalam merancang dan mengimplementasikan antarmuka (UI) untuk fitur-fitur baru pada aplikasi **PRANATA HRIS**.

---

## 1. Filosofi & Prinsip Desain

- **Clean & Modern SaaS Aesthetic**: Mengutamakan tampilan modern, rapi, minimalis, dan fungsional seperti software enterprise masa kini (Tailwind/Stripe/Linear style pada fondasi Bootstrap 5).
- **Consistent Enclosure (Card-Based)**: Hindari meletakkan tabel atau tombol yang mengambang lepas. Seluruh komponen konten utama (tabel, form, detail, filter) dibungkus rapi dalam kartu (`card`) terpadu.
- **Clear Information Hierarchy**: Setiap halaman dan modul memiliki judul yang jelas, ikon penanda, deskripsi sub-teks singkat, dan tombol tindakan utama yang kontras.
- **Soft Palette & High Legibility**: Menggunakan background netral yang bersih, batas garis tipis halus (`#e2e8f0` / `#f1f5f9`), dan badge warna pastel dengan opasitas lembut.
- **Mobile-First & Responsive Ready**: Seluruh tabel dibungkus dalam container horizontal scrolling (`overflow-x: auto`), tombol aksi responsif, serta layout form fleksibel di ponsel maupun desktop.

---

## 2. Design Tokens

### 2.1 Color Palette

| Token / Variabel | Nilai Hex / RGBA | Penggunaan |
| :--- | :--- | :--- |
| **Primary (Brand)** | `#2b5fe1` | Tombol utama, active tabs, header icon, highlight |
| **Primary Light / Bg** | `rgba(43, 95, 225, 0.1)` | Background avatar inisial, icon container, soft badge |
| **Success** | `#10b981` | Status Aktif, Disetujui (Approved), Hadir tepat waktu |
| **Success Soft Bg** | `rgba(16, 185, 129, 0.1)` | Badge status sukses, icon box kehadiran |
| **Warning** | `#f59e0b` | Status Pending, Terlambat, Checkout alert, Perhatian |
| **Warning Soft Bg** | `rgba(245, 158, 11, 0.1)` | Badge status pending, icon box warning |
| **Danger** | `#ef4444` | Status Ditolak (Reject), Absen/Mangkir, Hapus, Kuota habis |
| **Danger Soft Bg** | `rgba(239, 68, 68, 0.1)` | Badge error, alert bahaya |
| **Page Background** | `#fbfcff` | Background seluruh body aplikasi |
| **Card Background** | `#ffffff` | Background kartu, modul form, dan panel data |
| **Surface Light** | `#f8fafc` | Header tabel, card header dalam, box filter |
| **Border Default** | `#e2e8f0` | Garis tepi kartu, pembatas input, divider |
| **Border Subtle** | `#f1f5f9` | Garis pemisah baris tabel (row border) |
| **Text Heading** | `#0f172a` atau `#1e293b` | Judul halaman, heading kartu, teks tebal |
| **Text Body** | `#334155` | Teks isi tabel, nilai data, deskripsi |
| **Text Muted** | `#64748b` | Sub-judul, placeholder, helper text, label sekunder |

### 2.2 Typography

- **Font Family**: `'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif`
- **Headings**:
  - H1 / H2 Judul Halaman: `h4` atau `h5`, `fw-bold`, `text-dark` atau `text-primary`.
  - Heading Kartu: `h5` atau `h6`, `fw-bold text-dark mb-0`.
- **Micro-labels / Subtitle**: `small text-muted`, `font-size: 0.8rem - 0.875rem`.
- **Table Headers**: `font-size: 0.78rem - 0.82rem`, `font-weight: 600`, `text-transform: uppercase`, `letter-spacing: 0.03em`.

### 2.3 Iconography

- **Bootstrap Icons (`bi bi-*`)**: Digunakan sebagai ikon utama untuk navigasi, aksi, tombol, dan header.
- **FontAwesome (`fas fa-*`)**: Didukung untuk tombol aksi lama dan integrasi helper `GeneralHelper::fa*()`.

---

## 3. Global CSS Classes (`views/layouts/main.php`)

Gunakan class CSS standar berikut yang sudah terdaftar secara global di layout utama:

| Class CSS | Deskripsi |
| :--- | :--- |
| `.modern-grid-card` | Kartu pembungkus GridView dengan border radius `14px`, soft shadow, dan background putih. |
| `.modern-form-card` | Kartu pembungkus formulir input dengan padding terstruktur dan fokus input halus. |
| `.table-modern-detail`| Tampilan tabel DetailView tanpa border kaku dengan kolom label `#f8fafc`. |
| `.table-modern` | Styling tabel modern (head background `#f8fafc`, text `#475569`, padding proporsional). |

---

## 4. Pola Standar Komponen (Templates & Snippets)

### 4.1 Index / List View (Card + Header + Kartik GridView)

**Aturan:**
1. Selalu bungkus tabel dalam `<div class="card modern-grid-card shadow-sm border-0">`.
2. Header kartu memuat: Ikon modul, Judul Modul, Deskripsi sub-teks, dan Tombol Tambah di kanan.
3. Gunakan `card-body p-0` agar tabel membentang pas hingga tepi kartu.
4. Gunakan `'tableOptions' => ['class' => 'table table-hover align-middle mb-0 text-nowrap']`.

```php
<?php
use app\components\ButtonActionColumn;
use app\helpers\GeneralHelper;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Nama Modul';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card modern-grid-card shadow-sm border-0">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
        <div>
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-folder2 text-primary"></i> Data <?= Html::encode($this->title) ?>
            </h5>
            <span class="text-muted small">Kelola informasi dan daftar master <?= strtolower($this->title) ?></span>
        </div>
        <div>
            <?= Html::a(GeneralHelper::faAdd('Tambah ' . $this->title), ['process'], ['class' => 'btn btn-primary px-3 shadow-sm']) ?>
        </div>
    </div>
    <div class="card-body p-0">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'responsive' => true,
            'responsiveWrap' => false,
            'tableOptions' => ['class' => 'table table-hover align-middle mb-0 text-nowrap'],
            'containerOptions' => ['style' => 'overflow-x: auto;'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '<div class="fw-semibold text-dark">' . Html::encode($model->name) . '</div>';
                    }
                ],
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return GeneralHelper::textLabel($model->status ? 'Active' : 'Inactive', $model->status);
                    },
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                ],
                [
                    'class' => ButtonActionColumn::className(),
                    'template' => '{process} {delete}',
                    'urlCreator' => function ($action, $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->uuid]);
                    }
                ],
            ],
        ]); ?>
    </div>
</div>
```

---

### 4.2 Form / Process View (Input Data)

**Aturan:**
1. Bungkus dalam `<div class="card modern-form-card shadow-sm border-0">`.
2. Header kartu memuat judul tindakan (`Tambah` / `Update`) dan instruksi pengisian.
3. Susun input fields menggunakan Bootstrap Grid (`row g-3`).
4. Bagian footer aksi dibatasi garis `border-top` dengan tombol **Batal** (`btn-outline-secondary`) dan tombol **Simpan** (`btn-primary shadow-sm`).

```php
<?php
use app\helpers\GeneralHelper;
use kartik\form\ActiveForm;
use yii\helpers\Html;

$isNewRecord = $model->isNewRecord;
$this->title = $isNewRecord ? 'Tambah Modul' : 'Update Modul: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Modul', 'url' => ['index']];
$this->params['breadcrumbs'][] = $isNewRecord ? 'Tambah' : 'Update';
?>

<div class="card modern-form-card shadow-sm border-0">
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-pencil-square text-primary"></i> <?= Html::encode($this->title) ?>
        </h5>
        <span class="text-muted small">Lengkapi formulir di bawah ini dengan data yang valid</span>
    </div>
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Nama']) ?>
            </div>
            <div class="col-12 col-md-6">
                <?= $form->field($model, 'status')->dropDownList([1 => 'Active', 0 => 'Inactive'], ['class' => 'form-select']) ?>
            </div>
            <div class="col-12">
                <?= $form->field($model, 'description')->textarea(['rows' => 3, 'placeholder' => 'Deskripsi tambahan...']) ?>
            </div>
        </div>

        <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 mt-4 pt-3 border-top">
            <?= Html::a('Batal', ['index'], ['class' => 'btn btn-outline-secondary px-4 text-center']) ?>
            <?= Html::submitButton(GeneralHelper::faSave(), ['class' => 'btn btn-primary px-4 shadow-sm text-center']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
```

---

### 4.3 Detail / View Page

**Aturan:**
1. Sediakan tombol navigasi atas: `Kembali` di kiri/kanan, dan tombol aksi (`Ubah`, `Hapus`, `Approve`).
2. Gunakan `'options' => ['class' => 'table table-modern-detail mb-0']` pada `DetailView`.

```php
<?php
use yii\bootstrap5\Html;
use yii\widgets\DetailView;
use app\helpers\GeneralHelper;

$this->title = 'Detail: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Modul', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="modul-view">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <?= Html::a('<i class="bi bi-arrow-left me-1"></i> Kembali', ['index'], ['class' => 'btn btn-outline-secondary px-3 shadow-sm']) ?>

        <div class="d-flex gap-2">
            <?= Html::a(GeneralHelper::faUpdate('Ubah'), ['process', 'id' => $model->uuid], ['class' => 'btn btn-primary shadow-sm']) ?>
            <?= Html::a(GeneralHelper::faDelete('Hapus'), ['delete', 'id' => $model->uuid], [
                'class' => 'btn btn-outline-danger shadow-sm',
                'data' => [
                    'confirm' => 'Apakah Anda yakin ingin menghapus data ini?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <div class="card modern-form-card shadow-sm border-0">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-info-circle text-primary"></i> Rincian Informasi
            </h5>
            <div>
                <?= GeneralHelper::textLabel($model->status ? 'Active' : 'Inactive', $model->status) ?>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <?= DetailView::widget([
                    'model' => $model,
                    'options' => ['class' => 'table table-modern-detail mb-0'],
                    'attributes' => [
                        'name',
                        'description:ntext',
                        'created_at:datetime',
                        'updated_at:datetime',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
```

---

### 4.4 Metric / Stat Cards (Dashboard Style)

Gunakan pola 4-grid untuk menampilkan ringkasan analitik dan angka metrik:

```php
<div class="col-12 col-sm-6 col-xl-3">
    <div class="card h-100 border shadow-sm rounded-4 bg-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <span class="text-muted fw-semibold small">Label Metrik</span>
                <div class="rounded-3 d-flex align-items-center justify-content-center"
                    style="width: 44px; height: 44px; background: rgba(43, 95, 225, 0.1); color: #2b5fe1;">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
            </div>
            <div class="h2 fw-bold text-dark mb-1">1,240</div>
            <div class="small text-muted d-flex align-items-center gap-1">
                <i class="bi bi-check-circle text-success"></i> Keterangan status tambahan
            </div>
        </div>
    </div>
</div>
```

Warna tema metrik:
- **Blue**: `rgba(43, 95, 225, 0.1)` / `#2b5fe1`
- **Green**: `rgba(16, 185, 129, 0.1)` / `#10b981`
- **Amber**: `rgba(245, 158, 11, 0.1)` / `#f59e0b`
- **Purple**: `rgba(99, 102, 241, 0.1)` / `#6366f1`

---

### 4.5 Filter Card Pattern

Bila modul memerlukan filter tanggal atau kriteria pencarian di atas grid:

```php
<div class="card modern-form-card mb-4 shadow-sm border-0">
    <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-funnel text-primary"></i> Filter Pencarian
        </h5>
        <span class="text-muted small">Saring data sesuai kriteria</span>
    </div>
    <div class="card-body p-4">
        <?php $form = ActiveForm::begin(['method' => 'get']); ?>
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <?= $form->field($searchModel, 'attribute')->textInput() ?>
            </div>
            <div class="col-12 col-md-4 mb-3 d-flex gap-2">
                <?= Html::submitButton('<i class="bi bi-filter me-1"></i> Filter', ['class' => 'btn btn-primary px-4 shadow-sm flex-grow-1 text-center']) ?>
                <?= Html::a('<i class="bi bi-arrow-clockwise"></i>', ['index'], ['class' => 'btn btn-outline-secondary px-3 shadow-sm text-center', 'title' => 'Reset']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
```

---

### 4.6 Badges & Status Chips

Gunakan kombinasi warna pastel lembut dengan border tipis (soft badges):

```php
// Success (Aktif / Approve / Selesai)
'<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5"><i class="bi bi-check-circle me-1"></i>Aktif</span>'

// Warning / Pending
'<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5"><i class="bi bi-hourglass-split me-1"></i>Menunggu</span>'

// Danger / Inactive / Reject
'<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1.5"><i class="bi bi-x-circle me-1"></i>Ditolak</span>'

// Neutral / Light Chip
'<span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="bi bi-clock me-1 text-primary"></i>Shift Pagi</span>'
```

---

### 4.7 Empty States

Ketika query/tabel tidak memiliki baris data, tampilkan empty state yang ramah:

```php
<tr>
    <td colspan="100%" class="text-center py-5">
        <div class="text-muted mb-2"><i class="bi bi-inbox fs-1 opacity-50"></i></div>
        <div class="text-secondary fw-semibold">Belum ada data yang tersedia</div>
        <small class="text-muted">Klik tombol tambah di atas untuk membuat entri baru.</small>
    </td>
</tr>
```

---

## 5. Checklist Validasi UI untuk AI Agent

Sebelum menyelesaikan pembuatan atau modifikasi tampilan:
- [ ] Apakah seluruh tabel sudah dibungkus dalam `.card.modern-grid-card`?
- [ ] Apakah form sudah dibungkus dalam `.card.modern-form-card` dengan footer tombol aksi yang seragam?
- [ ] Apakah DetailView sudah menggunakan class `.table-modern-detail`?
- [ ] Apakah tabel responsif terhadap layar mobile (`responsive => true, containerOptions => ['style' => 'overflow-x: auto;']`)?
- [ ] Apakah warna badge status konsisten (lembut beropasitas, bukan solid pekat tanpa batas)?
- [ ] Apakah ikon Bootstrap Icons (`bi bi-*`) sudah digunakan secara kontekstual di judul header dan tombol?
- [ ] Apakah sintaks PHP valid (`php -l`) tanpa ada missing tag atau unclosed tag?
