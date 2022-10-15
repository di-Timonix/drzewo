<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Woods extends Model
{
    use HasFactory;

    protected $table = "wood";
    public $icon = false;
    public $updated_at = false;
    public $created_at = false;
    public $type = false;

}
