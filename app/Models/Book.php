<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // TODO: Insert your fillable fields
        'title',
        'description',
        'author',
        'year',
        'synopsis',
        'stock',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        // Nothing in hidden attributes
    ];

    // Helper function to make ensure that timestamps returned as format below
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    // Relationship function to Transaction Model
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }
}
