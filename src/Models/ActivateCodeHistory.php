<?php

namespace Larapress\Profiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int            $id
 * @property int            $activate_code_id
 * @property string         $ip
 * @property string         $user_agent
 * @property string         $session_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property ActivateCode   $activate_code
 */
class ActivateCodeHistory extends Model
{
    use SoftDeletes;

    protected $table = 'activate_codes_history';

    public $timestamps = true;

    public $fillable = [
        'activate_code_id',
        'ip',
        'user_agent',
        'session_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function activateCode()
    {
        return $this->belongsTo(ActivateCode::class, 'activate_code_id');
    }
}
