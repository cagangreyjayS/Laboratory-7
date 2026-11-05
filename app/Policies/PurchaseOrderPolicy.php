<?php

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseOrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any purchase orders.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view the list
    }

    /**
     * Determine whether the user can view the purchase order.
     */
    public function view(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create purchase orders.
     */
    public function create(User $user): bool
    {
        // Adjust role check based on your User model attributes (e.g., role, is_admin, etc.)
        return in_array($user->role ?? 'staff', ['admin', 'manager', 'warehouse', 'staff']);
    }

    /**
     * Determine whether the user can update the purchase order (Drafts only).
     */
    public function update(User $user, PurchaseOrder $purchaseOrder): bool
    {
        // Admins can edit anything; creators can edit their own drafts
        if ($user->role === 'admin') {
            return true;
        }

        return $purchaseOrder->status === 'draft' && $purchaseOrder->created_by === $user->id;
    }

    /**
     * Determine whether the user can approve or reject purchase orders (Managers / Admins).
     */
    public function approve(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return in_array($user->role ?? '', ['admin', 'manager']);
    }

    /**
     * Determine whether the user can receive items (Warehouse Staff / Admins).
     */
    public function receive(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return in_array($user->role ?? '', ['admin', 'warehouse']);
    }

    /**
     * Determine whether the user can delete the purchase order.
     */
    public function delete(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->role === 'admin' || ($purchaseOrder->status === 'draft' && $purchaseOrder->created_by === $user->id);
    }
}