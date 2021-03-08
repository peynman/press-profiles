<?php

namespace Larapress\Profiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property int $id
 * @property \Larapress\Profiles\Models\Domain $domain
 * @property int $domain_id
 */
class DomainSub extends Model
{
    use SoftDeletes;

    public $table = 'domains_subs';

    public $fillable = [
        'domain_id',
        'sub_domain',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domain()
    {
        return $this->belongsTo(
            Domain::class,
            'domain_id'
        );
    }
}
