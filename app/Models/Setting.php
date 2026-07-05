<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Remotelywork\Installer\Repository\App;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function add($key, $val, $type = 'string')
    {
        if (self::has($key)) {
            return self::set($key, $val, $type);
        }

        return self::create(['name' => $key, 'val' => $val, 'type' => $type]) ? $val : false;
    }

    public static function has($key)
    {
        if (! App::dbConnectionCheck() || app()->runningInConsole()) {
            return [];
        }

        return (bool) self::getAllSettings()->whereStrict('name', $key)->count();
    }

    public static function get($key, $section = null, $default = null)
    {
        if (self::has($key)) {
            $setting = self::getAllSettings()->where('name', $key)->first();

            return self::castValue($setting->val, $setting->type);
        }

        return self::getDefaultValue($key, $section, $default);
    }

    public static function getAllSettings()
    {
        if (! App::dbConnectionCheck() || app()->runningInConsole()) {
            return [];
        }

        return Cache::rememberForever('settings.all', function () {
            return self::all();
        });
    }

    public static function set($key, $val, $type = 'string')
    {
        if ($setting = self::getAllSettings()->where('name', $key)->first()) {
            return $setting->update([
                'name' => $key,
                'val' => $val,
                'type' => $type,
            ]) ? $val : false;
        }

        return self::add($key, $val, $type);
    }

    public static function remove($key)
    {
        if (self::has($key)) {
            return self::whereName($key)->delete();
        }

        return false;
    }

    public static function getValidationRules($section)
    {
        return self::getDefinedSettingFields($section)->pluck('rules', 'name')
            ->reject(function ($val) {
                return is_null($val);
            })->toArray();
    }

    private static function getDefinedSettingFields($section)
    {
        return collect(config('setting')[$section]['elements']);
    }

    public static function getDataType($field, $section)
    {
        $type = self::getDefinedSettingFields($section)
            ->pluck('data', 'name')
            ->get($field);

        return is_null($type) ? 'string' : $type;
    }

    public static function getValue($key, $section = null, $default = null)
    {
        if (self::has($key)) {
            $setting = self::getAllSettings()->where('name', $key)->first();

            return self::castValue($setting->val, $setting->type);
        }

        return self::getDefaultValue($key, $section, $default);
    }

    private static function castValue($val, $castTo)
    {
        if ($castTo === 'int' || $castTo === 'integer') {
            return intval($val);
        } elseif ($castTo === 'bool' || $castTo === 'boolean') {
            return boolval($val);
        } else {
            return $val;
        }
    }

    private static function getDefaultValue($key, $section, $default)
    {
        return is_null($default) ? self::getDefaultValueForField($key, $section) : $default;
    }

    public static function getDefaultValueForField($field, $section)
    {
        return self::getDefinedSettingFields($section)
            ->pluck('value', 'name')
            ->get($field);
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function () {
            self::flushCache();
        });

        static::created(function () {
            self::flushCache();
        });
    }

    public static function flushCache()
    {
        Cache::forget('settings.all');
    }
}
