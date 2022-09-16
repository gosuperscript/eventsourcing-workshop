<?php

namespace Workshop\Domains\Wallet\ReadModels;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Workshop\Domains\Wallet\WalletId;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $primaryKey = 'event_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $dates = ['transacted_at'];

    public static function scopeForWallet(Builder $query, string | WalletId $walletId)
    {
        if($walletId instanceof WalletId){
            $walletId = $walletId->toString();
        }
        return $query->where('wallet_id', $walletId);
    }
}
