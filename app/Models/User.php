<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\ManagersStore;
use App\Events\ManagerAdded;
use Helper;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array <int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function manager_stores() {
        return $this->hasMany('App\Models\ManagersStore', 'manager_id', 'id');
    }

    public function points() {
        return $this->hasMany('App\Models\Point', 'added_by', 'id');
    }

    public function saveStoreManager($data) {
        $password = Helper::generatePassword();
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->phone = $data['phone_number'];
        $this->password = bcrypt($password);
        $this->is_active = 1;
        $this->role = 1;
        if($this->exists) {
            $new_user = 0;
        }   else {
            $new_user = 1;
        }
        if($this->save()) {
            ManagersStore::where('manager_id', $this->id)->delete();
            foreach($data['stores'] as $store_id) {
                $manager = new ManagersStore();
                $manager->manager_id = $this->id;
                $manager->store_id = $store_id;
                $manager->save();
            }
            if($new_user == 1) {
                // Log::info("Event Fired: " . $this->email);
                event(new ManagerAdded($this, $password));
            }
            return true;
        }
        return false;
    }
}
