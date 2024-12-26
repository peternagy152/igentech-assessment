<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable  implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use InteractsWithMedia , HasApiTokens ,  HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone' ,
        "email_hashed" , 
        "phone_hashed" 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        "email" => 'encrypted' , 
        "phone" => 'encrypted' , 
    ] ; 

    public function devices():HasMany{
        return $this->hasMany(Device::class) ; 
    }

}
