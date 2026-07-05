<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected $guarded = [];

    protected $fillable = ['avatar', 'name', 'email', 'phone', 'password', 'device_token', 'is_admin', 'status'];

    protected function createdAt(): Attribute
    {
        return Attribute::make(get: function () {
            return Carbon::parse($this->attributes['created_at'])->format('M d Y h:i');
        });
    }

    /**
     * Get the full name of the admin.
     *
     * @param  string  $value
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    /**
     * Get the avatar path.
     *
     * @param  string  $value
     * @return string
     */
    public function getAvatarPathAttribute($value)
    {
        if ($this->avatar !== null) {
            $image_exists = file_exists(public_path($this->avatar));

            if ($image_exists) {
                return asset($this->avatar);
            }
        }

        return asset('global/materials/user.png');
    }
}
