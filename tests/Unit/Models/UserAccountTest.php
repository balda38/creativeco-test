<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Currency;
use App\Models\UserAccount;
use App\Models\UserAccountBuyTask;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserAccountTest extends TestCase
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
    private $userAccountBuyTask;

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
        $this->userAccountBuyTask = UserAccountBuyTask::factory()->create([
            'user_account_id' => $this->userAccount->id,
            'currency_id' => $this->currency2->id,
        ]);
    }

    protected function tearDonw(): void
    {
        parent::tearDown();

        $this->user = null;
        $this->currency1 = null;
        $this->currency2 = null;
        $this->userAccount = null;
        $this->userAccountBuyTask = null;
    }

    public function testUser()
    {
        $this->assertTrue($this->userAccount->user->is($this->user));
    }

    public function testCurrency()
    {
        $this->assertTrue($this->userAccount->currency->is($this->currency1));
    }

    public function testBuyTasks()
    {
        $this->assertCount(1, $this->userAccount->buyTasks);
        $this->assertTrue($this->userAccount->buyTasks->first()->is($this->userAccountBuyTask));
    }

    public function testScopeForUser()
    {
        $account = UserAccount::forUser($this->user)->first();
        $this->assertTrue($account->is($this->userAccount));
        $this->assertEquals($account->user_id, $this->user->id);
    }

    public function testScopeForCurrency()
    {
        $account = UserAccount::forCurrency($this->currency1)->first();
        $this->assertTrue($account->is($this->userAccount));
        $this->assertEquals($account->currency_id, $this->currency1->id);
    }

    public function testGetOwner()
    {
        $this->assertTrue($this->userAccount->getOwner()->is($this->user));
    }
}
