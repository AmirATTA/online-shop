<?php

namespace Modules\Area\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Kyslik\ColumnSortable\Sortable;
use Modules\Core\Casts\Date;
use Modules\Core\Entities\BaseModel;
use Modules\Core\Exceptions\ModelCannotBeDeletedException;
use Modules\Doctor\Entities\Doctor;
use Pricecurrent\LaravelEloquentFilters\Filterable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class City extends BaseModel
{
    use Sortable, Filterable, LogsActivity;

    protected $fillable = [
        'name', 'status'
    ];

    public $sortable = [
        'id', 'province_id', 'name', 'created_at'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->setDescriptionForEvent(fn(string $eventName) => 'شهر ' . __('logs.' . $eventName));
    }

    public static function clearAllCaches(): void
    {
        if (Cache::has('all_cities')) {
            Cache::forget('all_cities');
        }
    }

    public static function clearCitiesCacheByProvince(int $provinceId): void
    {
        if (Cache::has('cities_' . $provinceId)) {
            Cache::forget('cities_' . $provinceId);
        }
    }

    protected static function booted(): void
    {
        static::deleting(function (City $city) {
            if ($city->doctors()->exists()) {
                throw new ModelCannotBeDeletedException('این شهر قابل حذف نمی باشد چون در جدول دکترها استفاده شده است!');
            }
        });

        static::created(function () {
            static::clearAllCaches();
        });
        static::updated(function () {
            static::clearAllCaches();
        });
        static::saved(function () {
            static::clearAllCaches();
        });
        static::deleted(function () {
            static::clearAllCaches();
        });
    }

    public function isDeletable(): bool
    {
        return !$this->doctors()->exists();
    }

    public static function getAllCities(): \Illuminate\Support\Collection
    {
        return Cache::rememberForever('all_cities', function () {
            return City::query()
                ->where('status', 1)
                ->get(['id', 'name', 'province_id']);
        });
    }

    public function province(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function doctors(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Doctor::class);
    }
}
