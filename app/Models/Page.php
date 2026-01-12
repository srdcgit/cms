<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    // If table name differs from default (plural lowercase of model), specify it
    protected $table = 'pages';

    // Specify fillable fields for mass assignment
    protected $fillable = [
        'title',
        'slug',
        'html',
        'css',
        'js',
        'gjs_json',
    ];

    // If you want to disable timestamps (created_at, updated_at)
    // public $timestamps = false;

    // If primary key is not 'id', specify it
    // protected $primaryKey = 'id';
}
