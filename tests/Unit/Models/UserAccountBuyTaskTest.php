<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Currency;
use App\Models\UserAccount;
use App\Models\UserAccountBuyTask;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use Illuminate\Support\Carbon;

class UserAccountBuyTaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     * @psalm-ignore-var
     */
    private $user;
    /**
     * @var Currency
     * @psalm-ignore-var
     */
    private $currency1;
    /**
     * @var Currency
     * @psalm-ignore-var
     */
    private $currency2;
    /**
     * @var UserAccount
     * @psalm-ignore-var
     */
    private $userAccount;
    /**
     * @var UserAccount
     * @psalm-ignore-var
     */
    private $goalUserAccount;
    /**
     * @var UserAccountBuyTask
     * @psalm-ignore-var
     */
    private $expiredUserAccountBuyTask;
    /**
     * @var UserAccountBuyTask
     * @psalm-ignore-var
     */
    private $completedUserAccountBuyTask;
    /**
     * @var UserAccountBuyTask
     * @psalm-ignore-var
     */
    private $canceledUserAccountBuyTask;
    /**
     * @var UserAccountBuyTask
     * @psalm-ignore-var
     */
    private $waitingUserAccountBuyTask;

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
        $this->goalUserAccount = UserAccount::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $this->currency2->id,
        ]);
        $this->expiredUserAccountBuyTask = UserAccountBuyTask::factory()->create([
            'user_account_id' => $this->userAccount->id,
            'goal_user_account_id' => $this->goalUserAccount->id,
            'buy_before' => Carbon::now()->addYears(-1),
            'completed_at' => null,
            'canceled_at' => null,
        ]);
        $this->completedUserAccountBuyTask = UserAccountBuyTask::factory()->create([
            'user_account_id' => $this->userAccount->id,
            'goal_user_account_id' => $this->goalUserAccount->id,
            'completed_at' => Carbon::now()->addYears(-1),
            'canceled_at' => null,
        ]);
        $this->canceledUserAccountBuyTask = UserAccountBuyTask::factory()->create([
            'user_account_id' => $this->userAccount->id,
            'goal_user_account_id' => $this->goalUserAccount->id,
            'buy_before' => Carbon::now()->addYears(-1),
            'completed_at' => null,
            'canceled_at' => Carbon::now(),
        ]);
        $this->waitingUserAccountBuyTask = UserAccountBuyTask::factory()->create([
            'user_account_id' => $this->userAccount->id,
            'goal_user_account_id' => $this->goalUserAccount->id,
            'buy_before' => null,
            'completed_at' => null,
            'canceled_at' => null,
            'value' => 10,
            'count' => 10,
        ]);
    }

    protected function tearDonw(): void
    {
        parent::tearDown();

        $this->user = null;
        $this->currency1 = null;
        $this->currency2 = null;
        $this->userAccount = null;
        $this->goalUserAccount = null;
        $this->expiredUserAccountBuyTask = null;
        $this->completedUserAccountBuyTask = null;
        $this->canceledUserAccountBuyTask = null;
        $this->waitingUserAccountBuyTask = null;
    }

    public function testUserAccount()
    {
        $this->assertTrue($this->expiredUserAccountBuyTask->userAccount->is($this->userAccount));
    }

    public function testGoalUserAccount()
    {
        $this->assertTrue($this->expiredUserAccountBuyTask->goalUserAccount->is($this->goalUserAccount));
    }

    public function testScopeForUserAccount()
    {
        $buyTasks = UserAccountBuyTask::forUserAccount($this->userAccount)
            ->orderBy('id')
            ->get()
        ;
        $this->assertCount(4, $buyTasks);
        $this->assertTrue($buyTasks->first()->is($this->expiredUserAccountBuyTask));
        $this->assertEquals($buyTasks->first()->user_account_id, $this->userAccount->id);
    }

    public function testScopeForGoalUserAccount()
    {
        $buyTasks = UserAccountBuyTask::forGoalUserAccount($this->goalUserAccount)
            ->orderBy('id')
            ->get()
        ;
        $this->assertCount(4, $buyTasks);
        $this->assertTrue($buyTasks->first()->is($this->expiredUserAccountBuyTask));
        $this->assertEquals($buyTasks->first()->goal_user_account_id, $this->goalUserAccount->id);
    }

    public function testScopeExpired()
    {
        $buyTasks = UserAccountBuyTask::expired()->get();
        $this->assertCount(1, $buyTasks);
        $this->assertTrue($buyTasks->first()->is($this->expiredUserAccountBuyTask));
    }

    public function testScopeCompleted()
    {
        $buyTasks = UserAccountBuyTask::completed()->get();
        $this->assertCount(1, $buyTasks);
        $this->assertTrue($buyTasks->first()->is($this->completedUserAccountBuyTask));
    }

    public function testScopeCanceled()
    {
        $buyTasks = UserAccountBuyTask::canceled()->get();
        $this->assertCount(1, $buyTasks);
        $this->assertTrue($buyTasks->first()->is($this->canceledUserAccountBuyTask));
    }

    public function testScopeWaiting()
    {
        $buyTasks = UserAccountBuyTask::waiting()->get();
        $this->assertCount(1, $buyTasks);
        $this->assertTrue($buyTasks->first()->is($this->waitingUserAccountBuyTask));
    }

    public function testGetIsExpired()
    {
        $this->assertTrue($this->expiredUserAccountBuyTask->getIsExpired());
        $this->assertFalse($this->completedUserAccountBuyTask->getIsExpired());
        $this->assertFalse($this->canceledUserAccountBuyTask->getIsExpired());
        $this->assertFalse($this->waitingUserAccountBuyTask->getIsExpired());
    }

    public function testGetIsCompleted()
    {
        $this->assertFalse($this->expiredUserAccountBuyTask->getIsCompleted());
        $this->assertTrue($this->completedUserAccountBuyTask->getIsCompleted());
        $this->assertFalse($this->canceledUserAccountBuyTask->getIsCompleted());
        $this->assertFalse($this->waitingUserAccountBuyTask->getIsCompleted());
    }

    public function testGetIsCanceled()
    {
        $this->assertFalse($this->expiredUserAccountBuyTask->getIsCancled());
        $this->assertFalse($this->completedUserAccountBuyTask->getIsCancled());
        $this->assertTrue($this->canceledUserAccountBuyTask->getIsCancled());
        $this->assertFalse($this->waitingUserAccountBuyTask->getIsCancled());
    }

    public function testGetIsWaiting()
    {
        $this->assertFalse($this->expiredUserAccountBuyTask->getIsWaiting());
        $this->assertFalse($this->completedUserAccountBuyTask->getIsWaiting());
        $this->assertFalse($this->canceledUserAccountBuyTask->getIsWaiting());
        $this->assertTrue($this->waitingUserAccountBuyTask->getIsWaiting());
    }

    public function testGetOwner()
    {
        $this->assertTrue($this->expiredUserAccountBuyTask->getOwner()->is($this->user));
        $this->assertTrue($this->completedUserAccountBuyTask->getOwner()->is($this->user));
        $this->assertTrue($this->canceledUserAccountBuyTask->getOwner()->is($this->user));
    }

    public function testGetSum()
    {
        $this->assertEquals($this->waitingUserAccountBuyTask->getSum(), 100);
    }
}
