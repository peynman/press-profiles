<?php

namespace Larapress\Profiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Larapress\Profiles\IProfileUser;

/**
 * @property int       $id
 * @property array     $data
 * @property int       $flags
 * @property string    $type
 * @property string    $name
 * @property string    $title
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property IProfileUser $author
 */
class Filter extends Model
{
    use SoftDeletes;

    protected $table = 'filters';

    public $fillable = [
        'author_id',
        'type',
        'zorder',
        'name',
        'data',
        'flags',
    ];

    public $casts = [
        'data' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(config('larapress.crud.user.model'), 'author_id');
    }
}
