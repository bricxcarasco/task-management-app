<?php

namespace App\Models;

use App\Traits\ModelUpdatedTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NeoPrivacy extends Model
{
    use HasFactory;
    use ModelUpdatedTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'neo_id',
        'accept_connections',
        'accept_belongs',
    ];
}
