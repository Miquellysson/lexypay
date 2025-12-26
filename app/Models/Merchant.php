<?php

namespace App\Models;

use App\Payments\Support\ApiKeyHasher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Merchant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'api_key_hash',
        'status',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function matchesApiKey(string $plainTextKey): bool
    {
        return ApiKeyHasher::check($plainTextKey, $this->api_key_hash);
    }
}
