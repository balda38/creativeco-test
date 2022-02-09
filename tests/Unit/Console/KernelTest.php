<?php

namespace Tests\Unit\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Console\Scheduling\Event;

use Illuminate\Support\Collection;

use Tests\TestCase;

class KernelTest extends TestCase
{
    private function getEvents(string $command): Collection
    {
        $schedule = app()->make(Schedule::class);
        $events = collect($schedule->events())->filter(function (Event $event) use ($command) {
            return strpos($event->command, $command);
        });
        if (count($events) !== 1) {
            $this->fail('Command schedule not found or more than one');
        }

        return $events;
    }

    public function testParseCurrenciesSchedule()
    {
        $this->getEvents('parse:currencies')->each(function (Event $event) {
            $this->assertEquals('0 0 * * *', $event->expression);
        });
    }

    public function testParseExchangeRatesSchedule()
    {
        $this->getEvents('parse:exchangeRates')->each(function (Event $event) {
            $this->assertEquals('* * * * *', $event->expression);
        });
    }
}
