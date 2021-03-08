<?php

namespace Larapress\Profiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Larapress\CRUD\ICRUDUser;

/**
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property int            $id
 * @property int            $user_id
 * @property int            $domain_id
 * @property string         $email
 * @property int            $flags
 * @property ICRUDUser      $user
 * @property Domain         $domain
 */
class PhysicalAddress extends Model
{
    use SoftDeletes;

    protected $table = 'physical_addresses';

    public $timestamps = true;

    public $fillable = [
        'user_id',
        'domain_id',
        'type',
        'country_code',
        'city_code',
        'province_code',
        'address',
        'desc',
        'postal_code',
        'flags'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(
            config('larapress.crud.user.class'),
            'user_id',
            'id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }
}
