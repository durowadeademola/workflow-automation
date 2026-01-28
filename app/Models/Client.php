<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    public $table = 'clients';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'email',
        'telephone',
        'type',
        'status'
    ];

    public function users()     
    { 
        return $this->hasMany(User::class); 
    }

    public function agents()    
    { 
        return $this->hasMany(Agent::class); 
    }

    public function products()  
    { 
        return $this->hasMany(Product::class); 
    }

    public function customers() 
    { 
        return $this->hasMany(Customer::class); 
    }
}
