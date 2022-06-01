<?php

namespace Jncinet\Metaverse\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetaversePower extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id', 'metaverse_machine_id', 'quantity', 'count', 'price', 'power',
        'remaining_count', 'total_price', 'total_power'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'power' => 'decimal:8',
        'total_power' => 'decimal:8',
        'price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'user_id' => 'integer',
        'metaverse_machine_id' => 'integer',
        'quantity' => 'integer',
        'count' => 'integer',
        'remaining_count' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function metaverse_machine(): BelongsTo
    {
        return $this->belongsTo(MetaverseMachine::class);
    }

    /**
     * @return HasMany
     */
    public function metaverse_rewards(): HasMany
    {
        return $this->hasMany(MetaverseReward::class);
    }
}
