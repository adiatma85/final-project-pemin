<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // TODO: Insert your fillable fields
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        // Nothing to hidden
    ];

    // Helper function to make ensure that timestamps returned as format below
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    // Helper function that define the relationship to book
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    // Helper function that define the relationship to user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
