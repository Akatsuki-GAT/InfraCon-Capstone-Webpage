<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class construction_projects extends Model
{
    protected $table = 'construction_projects';
    protected $primaryKey = 'ConProjID';

    protected $fillable = [
        'createdBy',
        'projectName',
        'location',
        'startedDate',
        'targetEndDate',
        'actualEndDate',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'startedDate' => 'date',
            'targetEndDate' => 'date',
            'actualEndDate' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'createdBy', 'UserID');
    }

    public function accessRecords(): HasMany
    {
        return $this->hasMany(project_access_users::class, 'ConProjID', 'ConProjID');
    }

    public function accessibleUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'project_access_users',
            'ConProjID',
            'UserID',
            'ConProjID',
            'UserID'
        )->withTimestamps();
    }
}
