<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Image
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id
 * @property string $type 业务用途: avatar,topic,...
 * @property string $filesystem 所在文件系统:local,qiniu,...
 * @property string $path 文件路径
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image whereFilesystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Image whereUserId($value)
 * @property-read mixed $url
 * @property-read \App\Models\User $user
 */
class Image extends Model
{
    const TYPE_AVATAR = 'avatar';
    const TYPE_TOPIC = 'topic';

    const FILESYSTEM_LOCAL = 'local';

    protected $hidden = [
        'filesystem',
    ];

    protected $appends = [
        'url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute($value)
    {
        if ($this->filesystem == self::FILESYSTEM_LOCAL) {
            $value = config('app.url') . $value;
        }
        return $value;
    }
}
