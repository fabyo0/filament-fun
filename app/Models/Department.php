<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 *
 * @property-read Collection<int, Employee> $employee
 * @property-read int|null $employee_count
 *
 * @mixin \Eloquent
 */
class Department extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    public function employee(): HasMany
    {
        return $this->hasMany(related: Employee::class);
    }
}
