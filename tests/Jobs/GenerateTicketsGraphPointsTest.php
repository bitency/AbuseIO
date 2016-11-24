<?php

namespace tests\Jobs;

use AbuseIO\Jobs\GenerateTicketsGraphPoints;
use AbuseIO\Models\Ticket;
use AbuseIO\Models\TicketGraphPoint;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use tests\TestCase;

class GenerateTicketsGraphPointsTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

       // DB::table('tickets')->truncate();
    }

    public function testStoreNewTicketDataForToday()
    {
        factory(Ticket::class, 10)->create();
        $job = new GenerateTicketsGraphPoints();
        $job->storeNewTicketDataForToday();
        $this->assertEquals($this->countTicketsNewToday(), 10);
    }

    private function countTicketsNewToday()
    {
        return TicketGraphPoint::getNewDataPointsForToday()->reduce(function ($sum, $graphPoint) {
            return $sum + (int) ($graphPoint->count);
        });
    }

    public function testStoreTouchedTicketsForToday()
    {
        factory(Ticket::class, 10)->create([
            'created_at' => \Carbon::yesterday(),
            'updated_at' => \Carbon::today(),
        ]);

        $job = new GenerateTicketsGraphPoints();
        $job->storeTouchedDataForToday();
        $this->assertEquals($this->countTicketsTouchedToday(), 10);
    }

    private function countTicketsTouchedToday()
    {
        return TicketGraphPoint::getTouchedDataPointsForToday()->reduce(function ($sum, $graphPoint) {
            return $sum + (int) ($graphPoint->count);
        });
    }
}