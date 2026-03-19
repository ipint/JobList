<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\UkCounty;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        $department = fake()->randomElement([
            'Technology',
            'Sales',
            'Marketing',
            'Finance',
            'Human Resources',
            'Operations',
            'Customer Support',
            'Product',
            'Legal',
            'Procurement',
        ]);

        $titlesByDepartment = [
            'Technology' => ['PHP Developer', 'Backend Engineer', 'Laravel Developer', 'DevOps Engineer'],
            'Sales' => ['Account Executive', 'Business Development Manager', 'Sales Consultant', 'Partnerships Manager'],
            'Marketing' => ['Digital Marketing Executive', 'SEO Specialist', 'Content Manager', 'Brand Manager'],
            'Finance' => ['Management Accountant', 'Finance Analyst', 'Credit Controller', 'Payroll Manager'],
            'Human Resources' => ['HR Advisor', 'Talent Acquisition Specialist', 'People Partner', 'L&D Coordinator'],
            'Operations' => ['Operations Manager', 'Logistics Coordinator', 'Service Delivery Lead', 'Facilities Manager'],
            'Customer Support' => ['Customer Support Advisor', 'Customer Success Manager', 'Support Team Lead', 'Service Desk Analyst'],
            'Product' => ['Product Manager', 'Product Analyst', 'Delivery Manager', 'UX Researcher'],
            'Legal' => ['Paralegal', 'Compliance Officer', 'Contracts Manager', 'Legal Assistant'],
            'Procurement' => ['Buyer', 'Procurement Manager', 'Supply Chain Analyst', 'Category Manager'],
        ];

        $title = fake()->randomElement($titlesByDepartment[$department]);
        $companyName = fake()->randomElement([
            'Northbridge Digital',
            'Sterling Health Group',
            'Maple & Co',
            'Harbour Peak Solutions',
            'Apex Retail UK',
            'Bluegate Logistics',
            'Crownfield Energy',
            'Brightstone Financial',
            'Summit Education Partners',
            'Westbrook Services',
        ]);
        $countyId = UkCounty::query()->inRandomOrder()->value('id');
        $city = fake()->randomElement([
            'London',
            'Manchester',
            'Birmingham',
            'Leeds',
            'Liverpool',
            'Bristol',
            'Newcastle upon Tyne',
            'Sheffield',
            'Nottingham',
            'Leicester',
            'Reading',
            'Southampton',
            'Glasgow',
            'Edinburgh',
            'Cardiff',
            'Belfast',
        ]);
        $status = fake()->randomElement(['draft', 'published', 'published', 'published', 'expired']);
        $publishedAt = $status === 'draft' ? null : fake()->dateTimeBetween('-30 days', 'now');

        return [
            'title' => $title,
            'slug' => Str::slug($title . '-' . fake()->unique()->numerify('###??')),
            'reference' => 'JOB-' . fake()->unique()->numerify('#####'),
            'company_name' => $companyName,
            'department' => $department,
            'description' => fake()->paragraphs(3, true),
            'requirements' => fake()->paragraphs(2, true),
            'benefits' => fake()->sentence(12),
            'employment_type' => fake()->randomElement(['full_time', 'part_time', 'contract', 'temporary']),
            'work_mode' => fake()->randomElement(['on_site', 'hybrid', 'remote']),
            'experience_level' => fake()->randomElement(['entry', 'junior', 'mid', 'senior', 'lead']),
            'status' => $status,
            'county_id' => $countyId,
            'city' => $city,
            'postcode' => strtoupper(fake()->bothify('?## #??')),
            'location_name' => $city . ' Office',
            'salary_min' => fake()->numberBetween(25000, 65000),
            'salary_max' => fake()->numberBetween(66000, 95000),
            'salary_currency' => 'GBP',
            'salary_period' => fake()->randomElement(['year', 'day']),
            'salary_text' => fake()->optional()->randomElement(['Competitive salary', 'DOE', 'Plus bonus', 'Day rate negotiable']),
            'is_salary_visible' => fake()->boolean(85),
            'application_url' => fake()->url(),
            'application_email' => fake()->companyEmail(),
            'visa_sponsorship_available' => fake()->boolean(20),
            'right_to_work_required' => true,
            'closing_date' => fake()->dateTimeBetween('now', '+45 days'),
            'published_at' => $publishedAt,
            'expires_at' => fake()->optional(0.8)->dateTimeBetween('+15 days', '+60 days'),
            'is_featured' => fake()->boolean(20),
        ];
    }
}
