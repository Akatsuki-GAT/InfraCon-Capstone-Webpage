<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class project_access_users extends Model
{
    protected $table = 'project_access_users';
    protected $primaryKey = 'ProjectAccessID';

    protected $fillable = [
        'ConProjID',
        'UserID',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(construction_projects::class, 'ConProjID', 'ConProjID');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
