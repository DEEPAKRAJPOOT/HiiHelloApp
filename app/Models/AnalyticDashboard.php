<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticDashboard extends Model
{
    use HasFactory;

    protected $fillable = ['total_users', 'per_day_users', 'per_week_users', 'per_30_day_users', 'male_users', 'female_users', 'na_users', 'male_18_25', 'male_26_35', 'male_36_45', 'male_45', 'female_18_25', 'female_26_35', 'female_36_45', 'female_45', 'paid_users', 'non_paid_users', 'total_phone_users', 'total_google_users', 'total_facebook_users', 'total_apple_users', 'male_phone_verified', 'male_phone_unverified', 'male_email_verified', 'male_email_unverified', 'male_photo_verified', 'male_photo_unverified', 'male_account_verified', 'male_account_unverified', 'female_phone_verified', 'female_phone_unverified', 'female_email_verified', 'female_email_unverified', 'female_photo_verified', 'female_photo_unverified', 'female_account_verified', 'female_account_unverified'];
}
