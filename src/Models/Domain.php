<?php

namespace Larapress\Profiles\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\Factories\DomainFactory;
use Larapress\Profiles\IProfileUser;

/**
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property int            $id
 * @property string         $name
 * @property string         $title
 * @property string         $domain
 * @property string         $nameservers
 * @property string         ips
 * @property int            $flags
 * @property int            $status
 * @property int            $author_id
 * @property array          $data
 * @property IProfileUser   $author
 * @property IProfileUser[] $users
 * @property ICRUDUser[]    $affiliates
 * @property DomainSub[]    $sub_domains
 */
class Domain extends Model
{
    use HasFactory;

    const FLAG_DEFAULT_DOMAIN = 1;

    use SoftDeletes;

    protected $table = 'domains';

    protected $fillable = [
        'domain',
        'ips',
        'nameservers',
        'flags',
        'data',
        'author_id',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Undocumented function
     *
     * @return Factory
     */
    protected static function newFactory()
    {
        return DomainFactory::new();
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
    public function users()
    {
        return $this->belongsToMany(
            config('larapress.crud.user.class'),
            'user_domain',
            'domain_id',
            'user_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sub_domains()
    {
        return $this->hasMany(
            DomainSub::class,
            'domain_id'
        );
    }
}
