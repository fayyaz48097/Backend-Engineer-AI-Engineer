<?php

namespace Tests\Feature\Ticket;

use App\Jobs\AnalyzeTicketJob;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CreateTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_a_ticket_and_queues_the_analysis(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/tickets', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'subject' => 'Cannot login',
            'message' => 'I forgot my password and cannot login.',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('tickets', [
            'email' => 'ada@example.com',
            'status' => 'pending',
        ]);

        Queue::assertPushed(AnalyzeTicketJob::class);
    }

    public function test_it_rejects_an_invalid_ticket(): void
    {
        Queue::fake();

        $this->postJson('/api/tickets', ['name' => 'Ada'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'subject', 'message']);

        Queue::assertNothingPushed();
    }

    public function test_it_paginates_the_index(): void
    {
        Ticket::factory()->count(3)->create();

        $this->getJson('/api/tickets?per_page=2')
            ->assertOk()
            ->assertJsonPath('data.per_page', 2)
            ->assertJsonPath('data.total', 3)
            ->assertJsonCount(2, 'data.data');
    }
}
