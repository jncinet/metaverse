<?php

namespace Jncinet\Metaverse\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetaverseExchange extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id', 'amount', 'metaverse_user_level_id', 'rate', 'fees'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'fees' => 'decimal:8',
        'rate' => 'decimal:2',
        'amount' => 'decimal:8',
        'metaverse_user_level_id' => 'integer',
        'user_id' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function metaverse_user_level()
    {
        return $this->belongsTo(MetaverseUserLevel::class);
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
