<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    use HasFactory;
    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function coupon() {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }
    public function shipping() {
        return $this->belongsTo(ShippingMethod::class, 'shipping_id');
    }
    public function deposit() {
        return $this->hasOne(Deposit::class, 'order_id');
    }
    public function orderDetail() {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function scopePending() {
        return $this->where('order_status', 0);
    }
    public function scopeConfirmed() {
        return $this->where('order_status', 1);
    }

    public function scopeShipped() {
        return $this->where('order_status', 2);
    }
    public function scopeDelivered() {
        return $this->where('order_status', 3);
    }
    public function scopeCancel() {
        return $this->where('order_status', 9);
    }

    public function getStatusTextAttribute() {

        $class = "badge badge--";
        if ($this->order_status == 0) {
            $class .= 'warning';
            $text = 'Pending';
        } elseif ($this->order_status == 1) {
            $class .= 'success';
            $text = 'Confirmed';
        } elseif ($this->order_status == 2) {
            $class .= 'info';
            $text = 'Shipped';
        } elseif ($this->order_status == 3) {
            $class .= 'primary';
            $text = 'Delivered';
        }
         else {
            $class .= 'danger';
            $text = 'Cancelled';
        }

        return "<span class='$class'>" . trans($text) . "</span>";
    }
    public function getStatusTextButtonAttribute() {
        $text = '';
        if ($this->order_status == 0) {
            $text = 'Confirmed';
        } elseif ($this->order_status == 1) {
            $text = 'Shipped';
        } elseif ($this->order_status == 2) {
            $text = 'Delivered';
        }

        return trans($text);
    }
    public function getPaymentTextAttribute() {

        $class = "badge badge--";
        if ($this->payment_status == 0) {
            $class .= 'warning';
            $text = 'Pending';
        } elseif ($this->payment_status == 1) {
            $class .= 'success';
            $text = 'Success';
        }
         else {
            $class .= 'danger';
            $text = 'Cancelled';
        }
        return "<span class='$class'>" . trans($text) . "</span>";
    }


}
