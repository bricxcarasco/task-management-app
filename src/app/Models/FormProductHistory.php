<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormProductHistory extends Model
{
    use HasFactory;

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'form_product_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'form_history_id',
        'rio_id',
        'neo_id',
        'created_rio_id',
        'name',
        'quantity',
        'unit',
        'unit_price',
        'tax_distinction'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'amount',
    ];

    /**
    * Define relationship with FORM HISTORIES
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function form_histories()
    {
        return $this->belongsTo(FormHistory::class);
    }

    /**
     * Get amount attribute
     *
     * @return int|float
     */
    public function getAmountAttribute()
    {
        return (int)$this->quantity * (float)$this->unit_price ?? 0;
    }
}
