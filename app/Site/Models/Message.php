<?php

namespace App\Site\Models;

use App\MessageStatusEnum;
use Database\Factories\MessageFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'message',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => MessageStatusEnum::class,
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function markAsRead(): bool
    {
        return $this->update(['status' => MessageStatusEnum::READ]);
    }

    protected static function newFactory(): Factory
    {
        return MessageFactory::new();
    }
}
