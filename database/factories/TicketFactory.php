<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'subject' => fake()->sentence(),
            'message' => fake()->paragraph(),
            'status' => 'pending',
        ];
    }

    public function analyzed(): static
    {
        return $this->state(fn () => [
            'status' => 'completed',
            'category' => 'Billing',
            'priority' => 'Low',
            'summary' => 'Already summarised.',
            'suggested_reply' => 'Already replied.',
            'analyzed_at' => now(),
        ]);
    }
}
