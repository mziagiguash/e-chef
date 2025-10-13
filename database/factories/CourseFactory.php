<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CourseCategory;
use App\Models\Instructor;

class CourseFactory extends Factory
{
    public function definition()
    {
        $startDate = $this->faker->dateTimeBetween('now', '+1 year');

        return [
            'course_category_id' => CourseCategory::factory(),
            'instructor_id' => Instructor::factory(),
            'courseType' => $this->faker->randomElement(['free', 'paid', 'subscription']),
            'coursePrice' => $this->faker->randomFloat(2, 0, 1000),
            'courseOldPrice' => $this->faker->optional(0.7)->randomFloat(2, 1000, 2000),
            'subscription_price' => $this->faker->optional(0.5)->randomFloat(2, 10, 100),
            'start_from' => $startDate->format('Y-m-d'),
            'duration' => $this->faker->numberBetween(1, 52),
            'lesson' => $this->faker->numberBetween(5, 50),
            'course_code' => 'CRS-' . $this->faker->unique()->numberBetween(1000, 9999),
            'thumbnail_video_url' => $this->faker->optional()->url(),
            'tag' => $this->faker->optional()->randomElement(['popular', 'featured', 'upcoming']),
            'status' => $this->faker->randomElement([0, 1, 2]),
            'image' => $this->faker->imageUrl(800, 600, 'education', true),
            'thumbnail_image' => $this->faker->optional()->imageUrl(400, 300, 'education', true),
            'thumbnail_video_file' => $this->faker->optional()->filePath(),
        ];
    }
}
