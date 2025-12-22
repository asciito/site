<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements FilamentUser, HasAvatar, MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'description',
        'introduction',
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
            'password' => 'hashed',
        ];
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! app()->isProduction()) {
            return true;
        }

        return in_array($this->email, config('site.allowed_emails'));
    }

    public function getFilamentAvatarUrl(): ?string
    {
        /** @var string $colors */
        $colors = with(
            filament()->getPanel('webtools'),
            function (Panel $panel): string {
                $template = 'color=%s&background=%s';
                $bg = $panel->getColors()['primary'];
                $text = '0b0809';

                return sprintf(
                    $template,
                    $text,
                    trim($bg, '#'),
                );
            }
        );

        $name = Str::of($this->name)
            ->headline()
            ->explode(' ')
            ->map(fn ($word) => substr($word, 0, 1))
            ->splice(0, 2)
            ->join(' ');

        return "https://ui-avatars.com/api/?name=$name&$colors";
    }
}
