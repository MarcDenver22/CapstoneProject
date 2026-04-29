<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property string|null $employee_id
 * @property string|null $faculty_id
 * @property string|null $position
 * @property int|null $department_id
 * @property string|null $face_encodings
 * @property bool $face_enrolled
 * @property int $face_samples_count
 * @property \Carbon\Carbon|null $face_enrolled_at
 * @property \Carbon\Carbon|null $email_verified_at
 * @property \App\Models\Department|null $department
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $connection = 'supabase';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'employee_id',
        'faculty_id',
        'position',
        'department_id',
        'face_encodings',
        'face_enrolled',
        'face_samples_count',
        'face_enrolled_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
        ];
    }

    /**
     * Get the department associated with this user
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}