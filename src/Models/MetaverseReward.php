<?php

namespace Jncinet\Metaverse\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetaverseReward extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'type', 'amount', 'user_id', 'metaverse_power_id', 'metaverse_main_id',
        'metaverse_reward_id', 'status'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'type' => 'integer',
        'amount' => 'decimal:8',
        'user_id' => 'integer',
        'metaverse_power_id' => 'integer',
        'metaverse_main_id' => 'integer',
        'metaverse_reward_id' => 'integer',
        'status' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function metaverse_main()
    {
        return $this->belongsTo(MetaverseMain::class);
    }

    /**
     * @return BelongsTo
     */
    public function metaverse_reward()
    {
        return $this->belongsTo(MetaverseReward::class);
    }

    /**
     * @return BelongsTo
     */
    public function metaverse_power()
    {
        return $this->belongsTo(MetaversePower::class);
    }
}
