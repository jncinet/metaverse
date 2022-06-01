<?php

namespace Jncinet\Metaverse\Models;

use Illuminate\Database\Eloquent\Model;

class MetaverseTeamLevel extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = ['name', 'big_power', 'small_power', 'sort', 'main_reward_rate'];

    /**
     * @var string[]
     */
    protected $casts = [
        'name' => 'string',
        'small_power' => 'decimal:8',
        'big_power' => 'decimal:8',
        'sort' => 'integer',
        'main_reward_rate' => 'decimal:2',
    ];
}
