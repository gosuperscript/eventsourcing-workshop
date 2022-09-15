<?php

namespace Workshop\Domains\Wallet\ReadModels;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Workshop\Domains\Wallet\WalletId;

/**
 * @property int $balance
 * @property string $wallet_id
 */
class Balance extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $primaryKey = 'wallet_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public static function scopeForWallet(Builder $query, string|WalletId $walletId)
    {
        if ($walletId instanceof WalletId) {
            $walletId = $walletId->toString();
        }
        return $query->where('wallet_id', $walletId);
    }
}