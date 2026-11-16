<?php

namespace app\models\trx;

use app\helpers\DBHelper;
use app\models\BaseModel;
use app\models\master\Client;
use app\models\master\Company;
use app\models\master\Paket;
use app\models\User;
use Yii;

/**
 * This is the model class for table "subscription".
 *
 * @property int $id_subscription
 * @property int $id_client
 * @property int $id_company
 * @property int $id_paket
 * @property int $durasi
 * @property int $jumlah_bulan
 * @property float $price
 * @property int $status
 * @property int|null $approved_by
 * @property string|null $approved_at
 * @property string|null $note
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $uuid
 *
 * @property Client $client
 * @property Company $company
 * @property Paket $paket
 * @property User $approver
 */
class Subscription extends BaseModel
{
    const STATUS_PENDING = 0;
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_CANCELLED = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscription';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_client', 'id_company', 'id_paket', 'durasi'], 'required'],
            [['id_client', 'id_company', 'id_paket', 'durasi', 'jumlah_bulan', 'status', 'approved_by'], 'integer'],
            [['jumlah_bulan'], 'integer', 'min' => 1, 'max' => 120],
            [['price'], 'number'],
            [['note'], 'string'],
            [['created_at', 'updated_at', 'approved_at', 'uuid'], 'safe'],
            [['id_client'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['id_client' => 'id_client']],
            [['id_company'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['id_company' => 'id_company']],
            [['id_paket'], 'exist', 'skipOnError' => true, 'targetClass' => Paket::class, 'targetAttribute' => ['id_paket' => 'id_paket']],
            [['approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['approved_by' => 'id_user']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_subscription' => 'ID Subscription',
            'id_client' => 'Client',
            'id_company' => 'Perusahaan',
            'id_paket' => 'Paket',
            'durasi' => 'Durasi (Hari)',
            'jumlah_bulan' => 'Jumlah Bulan',
            'price' => 'Total Harga',
            'status' => 'Status',
            'approved_by' => 'Diproses Oleh',
            'approved_at' => 'Waktu Diproses',
            'note' => 'Catatan',
            'created_at' => 'Tanggal Pengajuan',
            'updated_at' => 'Terakhir Diperbarui',
        ];
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::class, ['id_client' => 'id_client']);
    }

    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id_company' => 'id_company']);
    }

    /**
     * Gets query for [[Paket]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPaket()
    {
        return $this->hasOne(Paket::class, ['id_paket' => 'id_paket']);
    }

    /**
     * Gets query for [[Approver]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getApprover()
    {
        return $this->hasOne(User::class, ['id_user' => 'approved_by']);
    }

    public function isPending(): bool
    {
        return (int) $this->status === self::STATUS_PENDING;
    }

    public function isActive(): bool
    {
        return (int) $this->status === self::STATUS_ACTIVE;
    }

    public function isCancelled(): bool
    {
        return (int) $this->status === self::STATUS_CANCELLED;
    }

    public static function getStatusList(): array
    {
        return [
            self::STATUS_PENDING => 'Menunggu Pembayaran',
            self::STATUS_ACTIVE => 'Disetujui / Aktif',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ];
    }

    public function getStatusLabel(): string
    {
        if ($this->isActive()) {
            return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5"><i class="bi bi-check-circle me-1"></i>Disetujui / Aktif</span>';
        }
        if ($this->isCancelled()) {
            return '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1.5"><i class="bi bi-x-circle me-1"></i>Dibatalkan</span>';
        }

        return '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5"><i class="bi bi-hourglass-split me-1"></i>Menunggu Pembayaran</span>';
    }

    /**
     * Effective months to add upon approval.
     */
    public function getEffectiveMonths(): int
    {
        if (!empty($this->jumlah_bulan) && (int) $this->jumlah_bulan > 0) {
            return (int) $this->jumlah_bulan;
        }

        return max(1, (int) round(((int) $this->durasi) / 30));
    }

    /**
     * Check if this subscription's package is an upgrade compared to the given current package.
     */
    public function isUpgradeFrom(?Paket $currentPaket): bool
    {
        if (!$currentPaket || !$this->paket) {
            return false;
        }

        return (int) $this->paket->max_user > (int) $currentPaket->max_user;
    }

    /**
     * Check if this subscription's package is a downgrade compared to the given current package.
     */
    public function isDowngradeFrom(?Paket $currentPaket): bool
    {
        if (!$currentPaket || !$this->paket) {
            return false;
        }

        return (int) $this->paket->max_user < (int) $currentPaket->max_user;
    }

    /**
     * Approve subscription and update company's expired_at.
     * - Same package: accumulate months from remaining expired_at (or from today if already expired/empty).
     * - Upgrade package: reset expired_at from today + months.
     */
    public function approve(int $userId): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $company = $this->company;
            if (!$company) {
                $transaction->rollBack();
                return false;
            }

            $currentActiveSub = self::find()
                ->where([
                    'id_company' => $company->id_company,
                    'status' => self::STATUS_ACTIVE,
                ])
                ->andWhere(['!=', 'id_subscription', $this->id_subscription])
                ->orderBy(['id_subscription' => SORT_DESC])
                ->one();
            $currentPaket = $currentActiveSub ? $currentActiveSub->paket : null;

            if ($this->isDowngradeFrom($currentPaket)) {
                $transaction->rollBack();
                return false;
            }

            $months = $this->getEffectiveMonths();
            $now = time();

            if ($this->isUpgradeFrom($currentPaket)) {
                // Naik paket: expired_at direset dari hari ini
                $baseTime = $now;
            } elseif (empty($company->expired_at) || strtotime($company->expired_at) <= $now) {
                // Paket sama & sudah kadaluarsa: ditambah dari hari ini
                $baseTime = $now;
            } else {
                // Paket sama & belum habis: diakumulasi dari tanggal expired_at
                $baseTime = strtotime($company->expired_at);
            }

            $company->expired_at = date('Y-m-d H:i:s', strtotime("+{$months} months", $baseTime));
            $company->status = 1;
            if ($this->paket && !empty($this->paket->max_user)) {
                $company->max_user = (int) $this->paket->max_user;
            }

            if (!$company->save(false)) {
                $transaction->rollBack();
                return false;
            }

            if ($company->client && (int) $company->client->is_active === 0) {
                $company->client->is_active = 1;
                $company->client->save(false);
            }

            $nowStr = DBHelper::now();
            $this->status = self::STATUS_ACTIVE;
            $this->approved_by = $userId;
            $this->approved_at = $nowStr;
            $this->updated_at = $nowStr;

            if (!$this->save(false)) {
                $transaction->rollBack();
                return false;
            }

            $transaction->commit();
            return true;
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }
    }

    /**
     * Cancel pending subscription.
     */
    public function cancel(int $userId): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        $nowStr = DBHelper::now();
        $this->status = self::STATUS_CANCELLED;
        $this->approved_by = $userId;
        $this->approved_at = $nowStr;
        $this->updated_at = $nowStr;

        return $this->save(false);
    }
}
