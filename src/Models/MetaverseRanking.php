<?php

namespace Jncinet\Metaverse\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetaverseRanking extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id', 'basic', 'addition', 'total', 'big_space', 'small_space', 'parent_id',
        'metaverse_user_level_id', 'metaverse_team_level_id', 'api_user_id', 'api_username'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'parent_id' => 'integer',
        'user_id' => 'integer',
        'metaverse_user_level_id' => 'integer',
        'metaverse_team_level_id' => 'integer',
        'basic' => 'decimal:8',
        'addition' => 'decimal:8',
        'total' => 'decimal:8',
        'big_space' => 'decimal:8',
        'small_space' => 'decimal:8',
        'api_user_id' => 'integer',
        'api_username' => 'string',
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
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * @return BelongsTo
     */
    public function metaverse_user_level(): BelongsTo
    {
        return $this->belongsTo(MetaverseUserLevel::class);
    }

    /**
     * @return BelongsTo
     */
    public function metaverse_team_level(): BelongsTo
    {
        return $this->belongsTo(MetaverseTeamLevel::class);
    }
}
