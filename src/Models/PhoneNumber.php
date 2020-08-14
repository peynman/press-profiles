<?php

namespace Larapress\Profiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Larapress\Core\Extend\Helpers;
use Larapress\CRUD\ICRUDUser;

/**
 * Class PhoneNumber.
 *
 * @property int            $id
 * @property int            $user_id
 * @property int            $domain_id
 * @property string         $number
 * @property int            $flags
 * @property ICRUDUser      $user
 * @property Domain         $domain
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class PhoneNumber extends Model
{
    const FLAGS_VERIFIED = 1;
    const FLAGS_DO_NOT_CONTACT = 2;

    use SoftDeletes;

    protected $table = 'phone_numbers';

    public $fillable = [
        'user_id',
        'domain_id',
        'number',
        'flags',
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
