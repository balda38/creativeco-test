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
     * @var UserAccount
     */
    private $goalUserAccount;
    /**
     * @var UserAccountBuyTask
     */
    private $expiredUserAccountBuyTask;
    /**
     * @var UserAccountBuyTask
     */
    private $completedUserAccountBuyTask;
    /**
     * @var UserAccountBuyTask
     */
    private $canceledUserAccountBuyTask;

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
            ->get();
        $this->assertCount(3, $buyTasks);
        $this->assertTrue($buyTasks->first()->is($this->expiredUserAccountBuyTask));
        $this->assertEquals($buyTasks->first()->user_account_id, $this->userAccount->id);
    }

    public function testScopeForGoalUserAccount()
    {
        $buyTasks = UserAccountBuyTask::forGoalUserAccount($this->goalUserAccount)
            ->orderBy('id')
            ->get();
        $this->assertCount(3, $buyTasks);
        $this->assertTrue($buyTasks->first()->is($this->expiredUserAccountBuyTask));
        $this->assertEquals($buyTasks->first()->goal_user_account_id, $this->goalUserAccount->id);
    }

    public function testScopeExpired()
    {
        $buyTasks = UserAccountBuyTask::expired()->get();
        $this->assertCount(1, $buyTasks);
        $this->assertTrue($buyTasks->first()->is($this->expiredUserAccountBuyTask));
    }

    public function testScopeCompeted()
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

    public function testGetIsExpired()
    {
        $this->assertTrue($this->expiredUserAccountBuyTask->getIsExpired());
        $this->assertFalse($this->completedUserAccountBuyTask->getIsExpired());
        $this->assertFalse($this->canceledUserAccountBuyTask->getIsExpired());
    }

    public function testGetIsCompleted()
    {
        $this->assertFalse($this->expiredUserAccountBuyTask->getIsCompleted());
        $this->assertTrue($this->completedUserAccountBuyTask->getIsCompleted());
        $this->assertFalse($this->canceledUserAccountBuyTask->getIsCompleted());
    }

    public function testGetIsCanceled()
    {
        $this->assertFalse($this->expiredUserAccountBuyTask->getIsCancled());
        $this->assertFalse($this->completedUserAccountBuyTask->getIsCancled());
        $this->assertTrue($this->canceledUserAccountBuyTask->getIsCancled());
    }

    public function testGetOwner()
    {
        $this->assertTrue($this->expiredUserAccountBuyTask->getOwner()->is($this->user));
        $this->assertTrue($this->completedUserAccountBuyTask->getOwner()->is($this->user));
        $this->assertTrue($this->canceledUserAccountBuyTask->getOwner()->is($this->user));
    }
}
