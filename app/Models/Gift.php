<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use File;

class Gift extends Model
{
    use HasFactory;
    protected $table = "gifts";
    protected $fillable = [
        'name', 'photo', 'points', 'is_active', 'created_by', 'available'
    ];

    protected $appends = [
        'photo_url'
    ];

    protected $casts = [
        'points' => 'integer',
        'id' => 'integer'
    ];

    public function getPhotoUrlAttribute() {
        if($this->attributes['photo'] == "") {
            return null;
        }
        return url('/uploads/gifts/'.$this->attributes['photo']);
    }

    public function order_gifts() {
        return $this->hasMany('App\Models\OrderGift', 'gift_id', 'id');
    }

    public function gift_stores() {
        return $this->hasMany('App\Models\GiftStore', 'gift_id', 'id');
    }

    public function saveGift($data) {
        $this->name = $data['name'];
        $this->points = $data['points'];
        if(isset($data['cropped_image_name'])) {
            $folderPath = public_path('uploads/gifts/');
            if (!file_exists($folderPath)) {
                // path does not exist
                mkdir($folderPath, 0777, true);
            }
            $image_parts = explode(";base64,", $data['cropped_image_name']);
            $image_type_aux = explode("image/", $image_parts[0]);
            // dd($image_parts);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);

            $imageName = uniqid() . date('YmdHis') . '.png';
            $imageFullPath = $folderPath.$imageName;
            file_put_contents($imageFullPath, $image_base64);
            $this->photo = $imageName;
        }
        $this->created_by = \Auth::user()->id;
        if($this->save()) {
            // GiftStore::where('gift_id', $this->id)->delete();
            foreach($data['stores'] as $store_id) {
                GiftStore::updateOrCreate([
                    'gift_id' => $this->id,
                    'store_id' => $store_id
                ], [
                    'gift_id' => $this->id,
                    'store_id' => $store_id
                ]);
                // $giftStore->gift_id = $this->id;
                // $giftStore->store_id = $store_id;
                // $giftStore->save();
            }
            GiftStore::where('gift_id', $this->id)->whereNotIn('store_id', $data['stores'])->delete();
            return true;
        }
        return false;
    }
}
