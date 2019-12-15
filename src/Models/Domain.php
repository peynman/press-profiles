<?php


namespace Larapress\Profiles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Larapress\CRUD\ICRUDUser;
use Larapress\Profiles\IProfileUser;

/**
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
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class Domain extends Model
{
    use SoftDeletes;

    protected $table = 'domains';

    protected $fillable = [
        'name',
        'title',
        'domain',
        'ips',
        'nameservers',
        'flags',
        'data',
        'author_id',
    ];

    protected $casts = [
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
     * @param Request|null $request
     *
     * @return Domain|null
     */
    public static function getRequestDomain(Request $request)
    {
        $domain_str = $request->getHost();
        return Domain::where('domain', $domain_str)->first();
    }

    /**
     * @param Request $request
     * @return bool
     */
    public static function isRequestDefaultDomain(Request $request)
    {
        $domain_str = $request->getHost();
        $sub_domain = Domain::where('domain', $domain_str)->first();
        $is_default_domain = false;
        if (is_null($sub_domain)) {
            $default_domain = config('app.url');
            $is_default_domain = stringContains($default_domain, $domain_str);
        }

        return $is_default_domain;
    }
}
