<?php

namespace Larapress\Profiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property \Larapress\Profiles\Models\Form $form
 * @property \Larapress\Profiles\IProfileUser $user
 * @property \Larapress\Profiles\Models\Domain $domain
 * @property int $id
 * @property int $form_id
 * @property int $user_id
 * @property int $domain_id
 * @property int $flags
 * @property string $tags
 * @property array $data
 */
class FormEntry extends Model
{
    use SoftDeletes;

    public $table = 'form_entries';

    public $fillable = [
        'form_id',
        'user_id',
        'domain_id',
        'tags',
        'data',
        'flags',
    ];

    public $casts = [
        'data' => 'array'
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
    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }
}
