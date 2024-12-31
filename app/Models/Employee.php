<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee query()
 *
 * @property-read City|null $city
 * @property-read Country|null $country
 * @property-read Department|null $department
 * @property-read State|null $state
 *
 * @mixin \Eloquent
 */
class Employee extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'country_id',
        'state_id',
        'city_id',
        'avatar',
        'department_id',
        'first_name',
        'last_name',
        'middle_name',
        'address',
        'zip_code',
        'date_of_birth',
        'date_hired',
    ];

    protected $appends = ['fullName'];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'date_hired' => 'date',
        ];
    }

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->first_name} {$this->last_name}"
        );
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(related: Country::class, foreignKey: 'country_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(related: State::class, foreignKey: 'state_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(related: City::class, foreignKey: 'city_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(related: Department::class, foreignKey: 'department_id');
    }
}
