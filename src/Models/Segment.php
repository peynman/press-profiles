<?php

namespace Larapress\Profiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Larapress\Profiles\IProfileUser;

/**
 * Class Segment.
 *
 * @property $id
 * @property string $name
 * @property int    $score
 * @property int    $flags
 * @property int    $author_id
 * @property array  $data
 * @property IProfileUser[] $members
 * @property IProfileUser $author
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class Segment extends Model
{
    use SoftDeletes;

    protected $table = 'segments';

    protected $fillable = [
        'author_id',
        'name',
        'score',
        'flags',
        'data',
    ];

    public $casts = [
        'data' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(
            config('larapress.crud.user.class'),
            'user_segment',
            'segment_id',
            'user_id'
        )->withPivot('created_at');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(
            config('larapress.crud.user.class'),
            'author_id',
        );
    }
}
