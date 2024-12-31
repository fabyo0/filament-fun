<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City query()
 *
 * @property-read \App\Models\State|null $state
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Employee> $employee
 * @property-read int|null $employee_count
 *
 * @mixin \Eloquent
 */
class City extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'state_id',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(related: State::class, foreignKey: 'state_id');
    }

    public function employee(): HasMany
    {
        return $this->hasMany(related: Employee::class);
    }
}
