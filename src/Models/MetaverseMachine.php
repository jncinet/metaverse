<?php

namespace Jncinet\Metaverse\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetaverseMachine extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'name', 'count', 'price', 'power', 'status', 'sort'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'name' => 'string',
        'power' => 'decimal:8',
        'price' => 'decimal:2',
        'count' => 'integer',
        'sort' => 'integer',
        'status' => 'integer',
    ];

    /**
     * @return HasMany
     */
    public function metaverse_powers(): HasMany
    {
        return $this->hasMany(MetaversePower::class);
    }
}
