<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class construction_progress extends Model
{
    protected $table = 'construction_progress';

    protected $primaryKey = 'ConProgressID';

    protected $fillable = [
        'ConProjID',
        'createdBy',
        'updatedBy',
        'progressName',
        'progressDesc',
        'startDate',
        'finishDate',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'startDate' => 'date',
            'finishDate' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(construction_projects::class, 'ConProjID', 'ConProjID');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'createdBy', 'UserID');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updatedBy', 'UserID');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(construction_tasks::class, 'ConProgressID', 'ConProgressID');
    }
}
