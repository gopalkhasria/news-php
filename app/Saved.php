<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Saved extends Model
{
   protected $fillable = ['title', 'description', 'image','url'];

}
