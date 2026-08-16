<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\back\doctors;
use App\Models\back\gnr_m_patients;
use App\Models\back\Question;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
//use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'roles_name',
        'verification_code',
        'Status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'roles_name'=>'array',
    ];

    public function doctor(): HasOne
    {
        return $this->hasOne(doctors::class,'user_id', 'id')->withDefault();
    }

    public function gnr_m_patients(): HasOne
    {
        return $this->hasOne(gnr_m_patients::class,'user_id', 'id');
    }
    public function Question(): HasOne
    {
        return $this->hasOne(Question::class,'user_id', 'id');
    }

    public function systemRoles(): Collection
    {
        return collect(Arr::wrap($this->roles_name))
            ->merge($this->getRoleNames())
            ->filter()
            ->map(fn ($role) => self::canonicalRoleName((string) $role))
            ->filter()
            ->unique()
            ->values();
    }

    public function hasSystemRole(string ...$roles): bool
    {
        $expected = collect($roles)
            ->map(fn ($role) => self::canonicalRoleName($role));

        return $this->systemRoles()->intersect($expected)->isNotEmpty();
    }

    public function primarySystemRole(): string
    {
        return (string) ($this->systemRoles()->first() ?: 'patient');
    }

    public static function canonicalRoleName(string $role): ?string
    {
        $normalized = strtolower(str_replace([' ', '-'], '_', trim($role)));

        return match ($normalized) {
            'super_admin', 'admin', 'administrator', 'مدير', 'مدير_النظام' => 'super_admin',
            'secretary', 'reception', 'receptionist', 'سكرتير', 'سكرتارية', 'استقبال' => 'secretary',
            'doctor', 'طبيب' => 'doctor',
            'patient', 'user', 'مريض', 'مستخدم' => 'patient',
            default => $normalized ?: null,
        };
    }

    public static function roleLabel(string $role): string
    {
        return match (self::canonicalRoleName($role)) {
            'super_admin' => 'مدير النظام',
            'secretary' => 'السكرتارية',
            'doctor' => 'طبيب',
            'patient' => 'مريض',
            default => 'مستخدم',
        };
    }

    public static function rules($id = 0)
    {
        return [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ];
    }

    public static function messages()
    {
        return [
            'required' => 'this field(:attribute) is required',
            'email.unique' => 'الايميل موجود مسبقا',
            'email.email' => 'صيغة الايميل غير صحيحة',
            'password.same' => 'تأكيد كلمة السر غير صحيحة',
        ];
    }

}
