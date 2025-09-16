<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instructor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contact', 'email', 'role_id', 'image', 'status',
        'password', 'language', 'access_block', 'remember_token'
    ];

    protected $dates = ['deleted_at'];

    protected $appends = [
        'translated_name',
        'translated_bio',
        'translated_designation',
        'translated_title',
        'display_name'
    ];

    // Связи
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function translations()
    {
        return $this->hasMany(InstructorTranslation::class, 'instructor_id');
    }

    // Методы для переводов
    public function getTranslation($field = null, $locale = null)
{
    $locale = $locale ?: app()->getLocale();

    if (!$this->relationLoaded('translations')) {
        $this->load('translations');
    }

    $translation = $this->translations->firstWhere('locale', $locale);

    if (!$translation) {
        $translation = $this->translations->firstWhere('locale', 'en');
    }

    if (!$translation) {
        $translation = $this->translations->first();
    }

    if (!$translation) {
        return $field ? null : null;
    }

    return $field ? $translation->$field : $translation;
}

public function getTranslatedNameAttribute()
{
    return $this->getTranslation('name') ?? 'No Name';
}



    public function getTranslatedBioAttribute()
    {
        return $this->getTranslation('bio');
    }

    public function getTranslatedDesignationAttribute()
    {
        return $this->getTranslation('designation');
    }

    public function getTranslatedTitleAttribute()
    {
        return $this->getTranslation('title');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->translated_name ?: 'No Name';
    }



    public function frontShow(): string
    {
        $name = $this->name ?: 'No Instructor';
        $designation = $this->designation ?: '';

        return $designation ? "$name — $designation" : $name;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeWithTranslations($query, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        return $query->with(['translations' => function($q) use ($locale) {
            $q->where('locale', $locale);
        }]);
    }

    public function scopeWithAllTranslations($query)
    {
        return $query->with('translations');
    }
        public function getNameAttribute()
    {
        $locale = app()->getLocale();
        $translation = $this->translations->where('locale', $locale)->first();
        return $translation->name ?? $this->translations->first()->name ?? '';
    }

    public function getBioAttribute()
    {
        $locale = app()->getLocale();
        $translation = $this->translations->where('locale', $locale)->first();
        return $translation->bio ?? $this->translations->first()->bio ?? '';
    }

    public function getTitleAttribute()
    {
        $locale = app()->getLocale();
        $translation = $this->translations->where('locale', $locale)->first();
        return $translation->title ?? $this->translations->first()->title ?? '';
    }

    public function getDesignationAttribute()
    {
        $locale = app()->getLocale();
        $translation = $this->translations->where('locale', $locale)->first();
        return $translation->designation ?? $this->translations->first()->designation ?? '';
    }


}
