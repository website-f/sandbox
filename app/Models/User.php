<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password'];
  protected $hidden = ['password','remember_token'];

  public function roles(){ return $this->belongsToMany(Role::class); }
  public function hasRole($name){ return $this->roles()->where('name',$name)->exists(); }

  public function profile(){ return $this->hasOne(Profile::class); }
  public function business(){ return $this->hasOne(Business::class); }
  public function education(){ return $this->hasOne(Education::class); }
  public function courses(){ return $this->hasMany(Course::class); }
  public function nextOfKin(){ return $this->hasOne(NextOfKin::class); }
  public function affiliations(){ return $this->hasMany(Affiliation::class); }

  public function referral(){ return $this->hasOne(Referral::class); }
  public function accounts(){ return $this->hasMany(Account::class); }
  public function subscriptions(){ return $this->hasMany(Subscription::class); }
}
