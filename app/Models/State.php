<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Testing\Fluent\Concerns\Has;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State query()
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\City> $city
 * @property-read int|null $city_count
 * @property-read \App\Models\Country|null $country
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Employee> $employee
 * @property-read int|null $employee_count
 *
 * @mixin \Eloquent
 */
class State extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'country_id',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(related: Country::class, foreignKey: 'country_id');
    }

    public function city(): HasMany
    {
        return $this->hasMany(related: City::class);
    }

    public function employee(): HasMany
    {
        return $this->hasMany(related: Employee::class);
    }
}
