<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Country query()
 *
 * @property-read Collection<int, State> $state
 * @property-read int|null $state_count
 * @property-read Collection<int, Employee> $employee
 * @property-read int|null $employee_count
 *
 * @mixin \Eloquent
 */
class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function state(): HasMany
    {
        return $this->hasMany(related: State::class);
    }

    public function employee(): HasMany
    {
        return $this->hasMany(related: Employee::class);
    }
}
