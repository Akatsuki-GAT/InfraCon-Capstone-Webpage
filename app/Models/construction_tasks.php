<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class construction_tasks extends Model
{
    protected $table = 'construction_tasks';

    protected $primaryKey = 'TaskID';

    protected $fillable = [
        'ConProgressID',
        'createdBy',
        'updatedBy',
        'taskName',
        'taskDesc',
        'startDate',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'startDate' => 'date',
        ];
    }

    public function progress(): BelongsTo
    {
        return $this->belongsTo(construction_progress::class, 'ConProgressID', 'ConProgressID');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'createdBy', 'UserID');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updatedBy', 'UserID');
    }
}
