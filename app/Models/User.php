<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'person_id',
        'role',
        'status',
        'registration_note',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship: User belongs to one Person in the tree
     */
    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is an editor
     */
    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }

    /**
     * Check if user is a viewer
     */
    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    /**
     * Check if user's account is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user's account is pending approval
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if user can edit a specific person
     */
    public function canEdit(Person $person): bool
    {
        // Admins can edit anyone
        if ($this->isAdmin()) {
            return true;
        }

        // Editors can only edit themselves
        if ($this->isEditor() && $this->person_id === $person->id) {
            return true;
        }

        // Viewers can't edit anyone
        return false;
    }

    /**
     * Check if user can delete a person
     */
    public function canDelete(Person $person): bool
    {
        // Only admins can delete
        return $this->isAdmin();
    }

    /**
     * Check if user can add children to a person
     */
    public function canAddChildTo(Person $person): bool
    {
        // Admins can add children anywhere
        if ($this->isAdmin()) {
            return true;
        }

        // Editors can add children to themselves
        if ($this->isEditor() && $this->person_id === $person->id) {
            return true;
        }

        return false;
    }

    /**
     * Scope: Only active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Only pending users
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
