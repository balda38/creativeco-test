<?php

namespace Tests\Unit\Events;

use App\Models\Currency;
use App\Models\CurrencyExchangeRate;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserAccountBuyTask;

use App\Events\CurrencyExchangeRatesSaved;
use App\Listeners\CurrencyExchangeListener;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CurrencyExchangeListenerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Currency
     */
    private $currency1;
    /**
     * @var Currency
     */
    private $currency2;
    /**
     * @var CurrencyExchangeRate
     */
    private $exchangeRate;
    /**
     * @var User
     */
    private $user;
    /**
     * @var UserAccount
     */
    private $userAccount;
    /**
     * @var UserAccount
     */
    private $goalUserAccount;
    /**
     * @var UserAccountBuyTask
     */
    private $userAccountBuyTask1;
    /**
     * @var UserAccountBuyTask
     */
    private $userAccountBuyTask2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currency1 = Currency::factory()->create();
        $this->currency2 = Currency::factory()->create();
        $this->exchangeRate = CurrencyExchangeRate::factory()->create([
            'from_currency_id' => $this->currency1->id,
            'to_currency_id' => $this->currency2->id,
            'value' => 50,
        ]);
        $this->user = User::factory()->create();
        $this->userAccount = UserAccount::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $this->currency2->id,
            'value' => 100,
        ]);
        $this->goalUserAccount = UserAccount::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $this->currency1->id,
            'value' => 0,
        ]);
        $this->userAccountBuyTask1 = UserAccountBuyTask::factory()->create([
            'user_account_id' => $this->userAccount->id,
            'goal_user_account_id' => $this->goalUserAccount->id,
            'value' => 100,
            'count' => 1,
            'completed_at' => null,
            'buy_before' => null,
            'canceled_at' => null,
        ]);
        $this->userAccountBuyTask2 = UserAccountBuyTask::factory()->create([
            'user_account_id' => $this->userAccount->id,
            'goal_user_account_id' => $this->goalUserAccount->id,
            'value' => 100,
            'count' => 2,
            'completed_at' => null,
            'buy_before' => null,
            'canceled_at' => null,
        ]);
    }

    protected function tearDonw(): void
    {
        parent::tearDown();

        $this->currency1 = null;
        $this->currency2 = null;
        $this->exchangeRate = null;
        $this->user = null;
        $this->userAccount = null;
        $this->goalUserAccount = null;
        $this->userAccountBuyTask1 = null;
        $this->userAccountBuyTask2 = null;
    }

    public function testHandleOnEmpty()
    {
        $listener = new CurrencyExchangeListener();
        $this->assertNull($listener->handle(new CurrencyExchangeRatesSaved([])));
    }

    public function testHandle()
    {
        $listener = new CurrencyExchangeListener();
        $listener->handle(new CurrencyExchangeRatesSaved([$this->exchangeRate]));

        $completed = UserAccountBuyTask::completed()->get();
        $this->assertCount(1, $completed);
        if (count($completed) === 1) {
            $this->assertTrue($completed[0]->is($this->userAccountBuyTask1));
        }
        $this->assertDatabaseHas('user_accounts', [
            'id' => $this->goalUserAccount->id,
            'value' => 1,
        ]);
        $this->assertDatabaseHas('user_accounts', [
            'id' => $this->userAccount->id,
            'value' => 50,
        ]);
    }
}
