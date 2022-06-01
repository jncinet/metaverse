<?php

namespace Jncinet\Metaverse\Models;

use Illuminate\Database\Eloquent\Model;

class MetaverseUserLevel extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'name', 'power', 'subordinates_number',
        'gas_fee_rate', 'sort',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'name' => 'string',
        'power' => 'decimal:8',
        'subordinates_number' => 'integer',
        'gas_fee_rate' => 'decimal:2',
        'sort' => 'integer',
    ];
}
