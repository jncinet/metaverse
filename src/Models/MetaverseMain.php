<?php

namespace Jncinet\Metaverse\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetaverseMain extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id', 'type', 'metaverse_exchange_id', 'amount'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'amount' => 'decimal:8',
        'user_id' => 'integer',
        'type' => 'integer',
        'metaverse_exchange_id' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function metaverse_exchange(): BelongsTo
    {
        return $this->belongsTo(MetaverseExchange::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
