<?php

namespace Larapress\Profiles\Models;

use Illuminate\Database\Eloquent\Model;
use Larapress\Profiles\IProfileUser;

/**
 * @property int            $id
 * @property int            $user_id
 * @property int            $domain_id
 * @property int            $type
 * @property string         $subject
 * @property string         $description
 * @property array          $data
 * @property IProfileUser   $user
 * @property Domain         $domain
 * @property \Carbon\Carbon $captured_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ActivityLog extends Model
{
    const TYPE_CRUD_CREATE = 1;
    const TYPE_CRUD_EDIT = 2;
    const TYPE_CRUD_DELETE = 3;
    const TYPE_CRUD_VERB = 4;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'domain_id',
        'type',
        'subject',
        'data',
        'description',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('larapress.crud.user.model'), 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }
}
