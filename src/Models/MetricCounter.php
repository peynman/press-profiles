<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Larapress\Profiles\Models\Domain;

/**
 * @property int            $id
 * @property string         $key
 * @property float          $value
 * @property Domain         $domain
 * @property \Carbon\Carbon $created_at
 */
class MetricCounter extends Model
{
    const UPDATED_AT = null;

    protected $table = 'metrics_counters';

    public $timestamps = [
        'created_at',
    ];

    protected $fillable = [
        'key',
        'value',
        'domain_id',
        'created_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }
}
