<?php

namespace App\Models;

use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'status',
        // Employee fields
        'employee_code',
        'phone',
        'emergency_contact',
        'date_of_birth',
        'gender',
        'national_id',
        'address',
        'department_id',
        'designation_id',
        'employment_type',
        'join_date',
        'confirmation_date',
        'resignation_date',
        'salary_type',
        'base_salary',
        'bank_name',
        'bank_account_number',
        'bank_branch',
        'daily_upload_target',
        'can_access_all_channels',
        'current_shift_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
        'bank_account_number',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'status' => UserStatus::class,
            'date_of_birth' => 'date',
            'join_date' => 'date',
            'confirmation_date' => 'date',
            'resignation_date' => 'date',
            'base_salary' => 'decimal:2',
            'daily_upload_target' => 'integer',
            'can_access_all_channels' => 'boolean',
        ];
    }


    // ==========================================
    // Status Methods
    // ==========================================

    /**
     * Check if the user is active.
     */
    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }

    /**
     * Check if the user is inactive.
     */
    public function isInactive(): bool
    {
        return $this->status === UserStatus::Inactive;
    }

    // ==========================================
    // Scopes
    // ==========================================

    /**
     * Scope to filter only active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', UserStatus::Active);
    }

    /**
     * Scope to filter only inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', UserStatus::Inactive);
    }

    /**
     * Scope to filter users with employee code (actual employees).
     */
    public function scopeEmployees($query)
    {
        return $query->whereNotNull('employee_code');
    }

    // ==========================================
    // Role & Permission Helpers
    // ==========================================

    /**
     * Check if user has only Employee role (no admin privileges).
     */
    public function isEmployeeOnly(): bool
    {
        return $this->hasRole('Employee') && ! $this->hasAnyRole(['Super Admin', 'Admin']);
    }

    /**
     * Check if user is admin or super admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['Super Admin', 'Admin']);
    }

    /**
     * Check if user can access a specific project.
     */
    public function canAccessProject(Project $project): bool
    {
        // Admins can access all projects
        if ($this->isAdmin()) {
            return true;
        }

        // Check if user is manager
        if ($project->manager_id === $this->id) {
            return true;
        }

        // Check if user is a member
        return $project->projectMembers()->where('user_id', $this->id)->exists();
    }

    /**
     * Scope projects this user can access.
     */
    public function accessibleProjectIds(): array
    {
        if ($this->isAdmin()) {
            return []; // Empty means all - handled in controller
        }

        // Get IDs of projects user is member of OR is manager of
        $memberProjectIds = ProjectMember::where('user_id', $this->id)->pluck('project_id')->toArray();
        $managedProjectIds = Project::where('manager_id', $this->id)->pluck('id')->toArray();

        return array_unique(array_merge($memberProjectIds, $managedProjectIds));
    }
}
