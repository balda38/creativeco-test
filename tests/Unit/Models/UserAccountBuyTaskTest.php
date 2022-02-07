<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Currency;
use App\Models\UserAccount;
use App\Models\UserAccountBuyTask;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

use Illuminate\Support\Carbon;

class UserAccountBuyTaskTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var User
     */
    private $user;
    /**
     * @var Currency
     */
    private $currency1;
    /**
     * @var Currency
     */
    private $currency2;
    /**
     * @var UserAccount
     */
    private $userAccount;
    /**
     * @var UserAccountBuyTask
     */
    private $expiredUserAccountBuyTask;
    /**
     * @var UserAccountBuyTask
     */
    private $completedUserAccountBuyTask;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->currency1 = Currency::factory()->create();
        $this->currency2 = Currency::factory()->create();
        $this->userAccount = UserAccount::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $this->currency1->id,
        ]);
        $this->expiredUserAccountBuyTask = UserAccountBuyTask::factory()->create([
            'user_account_id' => $this->userAccount->id,
            'currency_id' => $this->currency2->id,
            'buy_before' => Carbon::now()->addYears(-1),
            'completed_at' => null,
        ]);
        $this->completedUserAccountBuyTask = UserAccountBuyTask::factory()->create([
            'user_account_id' => $this->userAccount->id,
            'currency_id' => $this->currency2->id,
            'completed_at' => Carbon::now()->addYears(-1),
        ]);
    }

    protected function tearDonw(): void
    {
        parent::tearDown();

        $this->user = null;
        $this->currency1 = null;
        $this->currency2 = null;
        $this->userAccount = null;
        $this->expiredUserAccountBuyTask = null;
        $this->completedUserAccountBuyTask = null;
    }

    public function testUserAccountBuyTaskUserAccount()
    {
        $this->assertTrue($this->expiredUserAccountBuyTask->userAccount->is($this->userAccount));
    }

    public function testUserAccountBuyTaskCurrency()
    {
        $this->assertTrue($this->expiredUserAccountBuyTask->currency->is($this->currency2));
    }

    public function testUserAccountBuyTaskScopeForUserAccount()
    {
        $buyTasks = UserAccountBuyTask::forUserAccount($this->userAccount)
            ->orderBy('id')
            ->get();
        $this->assertCount(2, $buyTasks);
        $this->assertTrue($buyTasks->first()->is($this->expiredUserAccountBuyTask));
        $this->assertEquals($buyTasks->first()->user_account_id, $this->userAccount->id);
    }

    public function testUserAccountBuyTaskScopeForCurrency()
    {
        $buyTasks = UserAccountBuyTask::forCurrency($this->currency2)
            ->orderBy('id')
            ->get();
        $this->assertCount(2, $buyTasks);
        $this->assertTrue($buyTasks->first()->is($this->expiredUserAccountBuyTask));
        $this->assertEquals($buyTasks->first()->currency_id, $this->currency2->id);
    }

    public function testUserAccountBuyTaskScopeExpired()
    {
        $buyTasks = UserAccountBuyTask::expired()->get();
        $this->assertCount(1, $buyTasks);
        $this->assertTrue($buyTasks->first()->is($this->expiredUserAccountBuyTask));
    }

    public function testUserAccountBuyTaskGetIsExpired()
    {
        $this->assertTrue($this->expiredUserAccountBuyTask->getIsExpired());
        $this->assertFalse($this->completedUserAccountBuyTask->getIsExpired());
    }

    public function testUserAccountBuyTaskGetIsCompleted()
    {
        $this->assertFalse($this->expiredUserAccountBuyTask->getIsCompleted());
        $this->assertTrue($this->completedUserAccountBuyTask->getIsCompleted());
    }
}
