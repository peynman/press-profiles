<?php

namespace Larapress\Profiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property \Larapress\Profiles\IProfileUser $user
 * @property int $id
 * @property int $flags
 * @property int $user_id
 * @property string $client_type
 * @property string $client_agent
 * @property string $client_ip
 */
class Device extends Model
{
    use SoftDeletes;

    public $table = 'devices';

    public $fillable = [
        'user_id',
        'client_type',
        'client_agent',
        'client_ip',
        'flags',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('larapress.crud.user.class'), 'user_id');
    }
}
