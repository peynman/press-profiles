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
class EmailAddress extends Model
{
    const FLAGS_VERIFIED = 1;
    const FLAGS_DO_NOT_CONTACT = 2;

    use SoftDeletes;

    protected $table = 'email_addresses';

    public $timestamps = true;

    public $fillable = [
        'user_id',
        'domain_id',
        'email',
        'type',
        'flags',
        'desc',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(
            config('larapress.crud.user.model'),
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
