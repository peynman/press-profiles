<?php

namespace Larapress\Profiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Larapress\Profiles\IProfileUser;

/**
 * @property int       $id
 * @property int       $domain_id
 * @property array     $data
 * @property int       $flags
 * @property array     $translations
 * @property string    $type
 * @property string    $name
 * @property string    $title
 * @property Domain[]    $domains
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
        'name',
        'data',
        'flags',
        'translations',
    ];

    public $casts = [
        'data' => 'array',
        'translations' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function domains()
    {
        return $this->belongsToMany(
            Domain::class,
            'filter_domain',
            'filter_id',
            'domain_id'
        );
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(config('larapress.crud.user.class'), 'author_id');
    }

}
