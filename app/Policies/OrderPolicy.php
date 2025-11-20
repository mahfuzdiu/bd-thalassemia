<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Enums\UserRoleEnum;

class OrderPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        // Admin can update all
        if ($user->role === UserRoleEnum::ADMIN->value) {
            return true;
        }

        // Vendor can update only their own orders
        if ($user->role == UserRoleEnum::VENDOR->value) {
            return $order->user_id == $user->id;
        }
        // Others cannot update
        return false;
    }
}
