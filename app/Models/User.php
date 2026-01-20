<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'phone',
        'original_password',
        'profile_image',
        'signature_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'original_password',
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
            'password' => 'hashed',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->username)) {
                $user->username = static::generateUniqueUsername($user->name);
            }
        });

        static::updating(function ($user) {
            if ($user->isDirty('name')) {
                $user->username = static::generateUniqueUsername($user->name, $user->id);
            }
        });
    }

    /**
     * Generate a unique username from the given name.
     */
    protected static function generateUniqueUsername(string $name, $ignoreId = null): string
    {
        $base = Str::slug($name, '_');
        $username = $base;
        $counter = 2;

        // Keep incrementing until a unique username is found
        while (static::where('username', $username)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $username = "{$base}_{$counter}";
            $counter++;
        }

        return $username;
    }

    public function expertiseManagers()
    {
        return $this->belongsToMany(
            ExpertiseManager::class,
            'users_expertise_manager'
        )->withTimestamps();
    }

    public function scopeFilters($query, $filters = [], $columns = [])
    {
        // if (!empty($filters['date_range'])) {
        //     $explode = explode(' - ', $filters['date_range']);
        //     $from = Carbon::parse($explode[0])->format('Y-m-d H:i:s');
        //     $to = Carbon::parse($explode[1])->format('Y-m-d H:i:s');
        //     $query->whereDate('created_at', '>=', $from);
        //     $query->whereDate('created_at', '<=', $to);
        // }

        if (!empty($filters['search']) or !empty($filters['search']['value'])) {
            $term = is_array($filters['search']) ? $filters['search']['value'] : $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->orWhere('name', 'LIKE', '%' . $term . '%');
                $q->orWhere('email', 'LIKE', '%' . $term . '%');
                $q->orWhere('phone', 'LIKE', '%' . $term . '%');
            });
        }
        if (!empty($filters['sort'])) {
            $sort = $filters['sort'];

            if ($sort == 'latest') {
                $query->orderBy('id', 'desc');
            }
        }
        if (isset($filters['start']) && !empty($filters['length'])) {
            $query->take($filters['length'])
                ->skip($filters['start']);
        }
        if (!empty($filters['order']) and !empty(head($filters['order']))) {
            $order = head($filters['order']);
            $column = $columns[$order['column']];
            $query->orderBy($column, $order['dir']);
        }
        return $query;
    }
}
