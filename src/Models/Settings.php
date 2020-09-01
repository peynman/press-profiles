<?php

namespace Larapress\Profiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Larapress\Profiles\IProfileUser;

/**
 * @property int            $id
 * @property string         $key
 * @property string         $val
 * @property string         $type
 * @property IProfileUser   $user
 * @property int            $user_id
 * @property IProfileUser   $author
 * @property int            $author_id
 * @property Domain[]       $domains
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class Settings extends Model
{
    use SoftDeletes;

    protected $table = 'settings';

    public $fillable = [
        'user_id',
        'author_id',
        'key',
        'val',
        'type',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('larapress.crud.user.class'), 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(config('larapress.crud.user.class'), 'author_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function domains()
    {
        return $this->belongsToMany(
            Domain::class,
            'setting_domain',
            'setting_id',
            'domain_id'
        );
    }
}
