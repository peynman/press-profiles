<?php

namespace Larapress\Profiles\Models;

use Larapress\Profiles\Flags\ActivateCodeMode;
use Larapress\Profiles\Flags\ActivateCodeStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Larapress\CRUD\ICRUDUser;

/**
 * @property \Carbon\Carbon        $created_at
 * @property \Carbon\Carbon        $updated_at
 * @property \Carbon\Carbon        $deleted_at
 * @property int                   $id
 * @property string                $code
 * @property string                $mode
 * @property int                   $status
 * @property int                   $user_id
 * @property int                   $student_id
 * @property ICRUDUser             $user
 * @property ActivateCodeHistory[] $history
 * @property array                 $data
 */
class ActivateCode extends Model
{
    use SoftDeletes;

    protected $table = 'activate_codes';

    public $timestamps = true;

    public $fillable = [
        'user_id',
        'code',
        'mode',
        'status',
        'data',
    ];

    public $casts = [
        'data' => 'array'
    ];

    public function isMobile()
    {
        return $this->mode === ActivateCodeMode::BROWSER_MOBILE;
    }
    public function isPC()
    {
        return $this->mode === ActivateCodeMode::BROWSER_DESKTOP;
    }
    public function isUsed()
    {
        return $this->status === ActivateCodeStatus::USED;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('larapress.core.user.class'), 'user_id', 'id');
    }

    public function history()
    {
        return $this->hasMany(ActivateCodeHistory::class, 'activate_code_id', 'id');
    }
}
