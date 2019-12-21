<?php

namespace Larapress\CRUD\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Larapress\Core\Extend\SelectorObjects;
use Larapress\Profiles\Models\Domain;

/**
 * @property int       $id
 * @property int       $domain_id
 * @property array     $data
 * @property int       $flags
 * @property array     $translations
 * @property string    $type
 * @property string    $name
 * @property string    $title
 * @property Domain    $domain
 */
class Filter extends Model
{
    use SelectorObjects;

    protected $table = 'filters';

    public $timestamps = false;

    public $fillable = [
        'type',
        'name',
        'title',
        'domain_id',
        'data',
        'flags',
        'translations',
    ];

    public $casts = [
        'data' => 'array',
        'translations' => 'array',
    ];

    /**
     * @return BelongsTo
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }

    /**
     * @param        $tagsCandid
     * @param string $type
     *
     * @return mixed
     */
    public static function processTags($tagsCandid, $type = '')
    {
        $tags_array = [];
        if (is_string($tagsCandid)) {
            $tags_array = json_decode($tagsCandid);
            if (is_null($tags_array) || ! is_object($tags_array)) {
                $tags_array = [$tagsCandid];
            }
        } elseif (is_array($tagsCandid)) {
            $tags_array = $tagsCandid;
        }
        /** @var Filter[] $tags */
        $tags = self::whereIn('id', $tags_array)->get();
        if (count($tags) < count($tags_array)) {
            foreach ($tags_array as $new_tag) {
                $new = true;
                foreach ($tags as $tag) {
                    if ($new_tag == $tag->name) {
                        $new = false;
                        break;
                    }
                }

                if ($new) {
                    $created = self::create([
                        'name' => $new_tag,
                        'type' => $type,
                        'flags' => 0,
                    ]);
                    $tags[] = $created;
                }
            }
        }

        return $tags;
    }

    /**
     * @param string $type
     *
     * @return Filter[]
     */
    public static function getByType($type)
    {
        $objects = self::getSelectorObjects(['id', 'type', 'name', 'title', 'data'], false);
        $tags = [];
        foreach ($objects as $object) {
            if ($object->type == $type) {
                $tags[] = $object;
            }
        }

        return $tags;
    }

    public static function randomByType($type)
    {
        $objects = self::getByType($type);
        if (count($objects) > 1) {
            return $objects[rand(0, count($objects) - 1)];
        }

        return $objects[0];
    }
}
