<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Interfaces\AutoFilterable;
use App\Models\Chat\Conversation;
use App\Models\Property\Property;
use App\Traits\IsAutoFilterable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 *
 * @property int $id Primary
 * @property mixed $first_name
 * @property mixed $last_name
 * @property mixed $type
 * @property mixed $is_admin
 * @property mixed $image
 * @property mixed $mobile
 * @property mixed $email
 * @property \Carbon\Carbon $email_verified_at
 * @property mixed $password
 * @property mixed $remember_token
 * @property mixed $is_blacklisted
 * @property string $blacklist_reason
 * @property mixed $is_active
 * @property string $is_active_reason
 * @property \Carbon\Carbon $subscription_start
 * @property \Carbon\Carbon $subscription_end
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @package App\Models
 */
class User extends Authenticatable implements AutoFilterable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;
    use IsAutoFilterable;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'users';
    protected $guarded = [
        'id',
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

    protected $appends = [
        'image_url',
        'name',
    ];



    /**
     * {@inheritdoc}
     * Defines the whitelist of columns that can be filtered by the front-end.
     * This includes columns from the main table and the translation table.
     */
    public function getFilterableColumns(): array
    {
        return [
            'id',
            'first_name',
            'last_name',
            'type',
            'is_admin',
            'image',
            'mobile',
            'email',
            'email_verified_at',
            'password',
            'remember_token',
            'is_blacklisted',
            'blacklist_reason',
            'is_active',
            'is_active_reason',
            'subscription_start',
            'subscription_end',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * {@inheritdoc}
     * Defines the whitelist of columns that can be used for sorting.
     */
    public function getSortableColumns(): array
    {
        return [
            'id',
            'first_name',
            'last_name',
            'type',
            'is_admin',
            'image',
            'mobile',
            'email',
            'email_verified_at',
            'password',
            'remember_token',
            'is_blacklisted',
            'blacklist_reason',
            'is_active',
            'is_active_reason',
            'subscription_start',
            'subscription_end',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * {@inheritdoc}
     * Defines the columns from the main table to be included in the global search.
     */
    public function getSearchableColumns(): array
    {
        return [
            'first_name',
            'last_name',
            'mobile',
            'email',
        ];
    }



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
            'is_admin' => 'boolean',
            'is_blacklisted' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['first_name'] . ' ' . $attributes['last_name']
        );
    }

    // public function imageUrl(): Attribute
    // {
    //     return  Attribute::get(function () {

    //         $image = $this->image;
    //         return !is_null($image) ?  Storage::disk('public')->url(USERS_IMAGE_NAME . '/' . $image) :
    //             config('app.web_config.default_user_image');
    //         // (Storage::disk('public')->url(USERS_IMAGE_NAME . '/' . 'default.jpg');
    //     });
    // }

    public function hasCustomAvatar()
    {
        // افترض أن الصورة الافتراضية لها مسار معين أو أن الحقل يكون null
        // عدّل هذا الشرط ليناسب منطق تطبيقك
        return $this->image != null && !str_contains($this->image, 'defaults/avatar.png');
    }

    function isAdmin()
    {
        return $this->is_admin;
    }

    public function imageUrl(): Attribute
    {
        $image = $this->image;

        $value = !is_null($image) ?  asset(USERS_IMAGE_NAME . '/' . $image) :
            config('app.web_config.default_user_image');

        return new Attribute(
            get: fn() => $value,
        );
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'user_id', 'id');
    }

    public function favorite(): HasMany
    {
        return $this->hasMany(Favorite::class, 'user_id', 'id');
    }

    public function privateComments(): HasMany
    {
        return $this->hasMany(UserPrivateComment::class, 'user_id', 'id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteProperties() // More convenient for getting the actual properties
    {
        return $this->belongsToMany(Property::class, 'favorites', 'user_id', 'property_id')->withTimestamps();
    }

    public function blacklistedPropertiesRelation(): HasMany // Naming to avoid conflict if you have a direct 'blacklistedProperties' attribute
    {
        return $this->hasMany(UserBlackList::class);
    }

    public function blacklistedProps() // More convenient for getting the actual properties
    {
        return $this->belongsToMany(Property::class, 'blacklisted_properties', 'user_id', 'property_id')->withTimestamps();
    }

    // Helper methods
    public function hasFavorited(Property $property): bool
    {
        return $this->favorites()->where('property_id', $property->id)->exists();
    }

    public function hasBlacklisted(Property $property): bool
    {
        return $this->blacklistedPropertiesRelation()->where('property_id', $property->id)->exists();
    }


    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
            ->withPivot('read_at') // مهم لجلب حالة القراءة
            ->withTimestamps();
    }

    public function newsletterSubscription()
    {
        return $this->hasOne(NewsletterSubscriber::class, 'email', 'email');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class)->latest();
    }
}
