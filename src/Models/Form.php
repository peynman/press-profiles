<?php

namespace Larapress\Profiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property \Larapress\Profiles\IProfileUser $author
 * @property \Larapress\Profiles\Models\Domain[] $domains
 * @property int $id
 * @property array $data
 * @property string $name
 * @property int $flags
 * @property int $author_id
 * @property string $description
 */
class Form extends Model
{
    use SoftDeletes;

    public $table = 'forms';

    public $fillable = [
        'name',
        'flags',
        'data',
        'description',
        'author_id',
    ];


    public $casts = [
        'data' => 'array'
    ];

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
    public function entries()
    {
        return $this->hasMany(
            FormEntry::class,
            'form_id',
            'id'
        );
    }
}
