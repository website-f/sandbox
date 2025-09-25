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

  public function assignRole($roleName)
    {
        $role = Role::where('name', $roleName)->firstOrFail();
        $this->roles()->syncWithoutDetaching([$role->id]);
    }
    public function removeRole($roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->roles()->detach($role->id);
        }
    }

    public function wallet()
{
    return $this->hasOne(Wallet::class);
}

public function referrer()
{
    return $this->hasOneThrough(
        User::class,      
        Referral::class,   
        'user_id',        
        'id',              
        'id',              
        'parent_id'       
    );
}



  public function profile(){ return $this->hasOne(Profile::class); }
  public function business(){ return $this->hasOne(Business::class); }
  public function education(){ return $this->hasOne(Education::class); }
  public function courses(){ return $this->hasMany(Course::class); }
  public function nextOfKin(){ return $this->hasOne(NextOfKin::class); }
  public function affiliations(){ return $this->hasMany(Affiliation::class); }

  public function referral(){ return $this->hasOne(Referral::class); }
  public function referrals()
{
    // all Referral records where this user is the parent/upline
    return $this->hasMany(Referral::class, 'parent_id', 'id');
}

  public function accounts(){ return $this->hasMany(Account::class); }
  public function subscriptions(){ return $this->hasMany(Subscription::class); }

  public function checkBlacklist(): bool
    {
        return Blacklist::where('email', $this->email)->exists();
    }
  
}
