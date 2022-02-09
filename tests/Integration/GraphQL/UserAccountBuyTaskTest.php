<?php

namespace Tests\Integration\GraphQL;

use App\Models\Currency;
use App\Models\CurrencyExchangeRate;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserAccountBuyTask;

use Illuminate\Support\Carbon;

class UserAccountBuyTaskTest extends TestCase
{
    /**
     * @var User
     */
    private $otherUser;
    /**
     * @var Currency
     */
    private $currency1;
    /**
     * @var Currency
     */
    private $currency2;
    /**
     * @var Currency
     */
    private $archivedCurrency;
    /**
     * @var CurrencyExchangeRate
     */
    private $currencyExchangeRate;
    /**
     * @var UserAccount
     */
    private $userAccount1;
    /**
     * @var UserAccount
     */
    private $userAccount2;
    /**
     * @var UserAccount
     */
    private $userAccount3;
    /**
     * @var UserAccount
     */
    private $userAccountWithArchivedCurrency;
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

        $this->otherUser = User::factory()->create();
        $this->currency1 = Currency::factory()->create();
        $this->currency2 = Currency::factory()->create();
        $this->archivedCurrency = Currency::factory()->create([
            'archived' => true,
        ]);
        $this->currencyExchangeRate = CurrencyExchangeRate::factory()->create([
            'from_currency_id' => $this->currency2->id,
            'to_currency_id' => $this->currency1->id,
        ]);
        $this->userAccount1 = UserAccount::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $this->currency1->id,
        ]);
        $this->userAccount2 = UserAccount::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $this->currency2->id,
        ]);
        $this->userAccount3 = UserAccount::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $this->currency2->id,
        ]);
        $this->userAccountWithArchivedCurrency = UserAccount::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $this->archivedCurrency->id,
        ]);
        $this->userAccountBuyTask1 = UserAccountBuyTask::factory()->create([
            'user_account_id' => $this->userAccount1->id,
            'goal_user_account_id' => $this->userAccount2->id,
            'completed_at' => null,
            'buy_before' => null,
            'canceled_at' => null,
        ]);
        $this->userAccountBuyTask2 = UserAccountBuyTask::factory()->create([
            'user_account_id' => $this->userAccount1->id,
            'goal_user_account_id' => $this->userAccount2->id,
            'completed_at' => Carbon::now(),
            'buy_before' => null,
            'canceled_at' => null,
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->otherUser = null;
        $this->currency1 = null;
        $this->currency2 = null;
        $this->archivedCurrency = null;
        $this->userAccount1 = null;
        $this->userAccount2 = null;
        $this->userAccount3 = null;
        $this->userAccountWithArchivedCurrency = null;
        $this->userAccountBuyTask1 = null;
        $this->userAccountBuyTask2 = null;
    }

    public function testGetUserAccountBuyTask()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            {
                userAccountBuyTask(id: {$this->userAccountBuyTask1->id}) {
                    id
                    userAccount {
                        id
                        currency {
                            id
                        }
                    }
                    goalUserAccount {
                        id
                        currency {
                            id
                        }
                    }
                }
            }
        ")->assertJson([
            'data' => [
                'userAccountBuyTask' => [
                    'id' => $this->userAccountBuyTask1->id,
                    'userAccount' => [
                        'id' => $this->userAccount1->id,
                        'currency' => [
                            'id' => $this->currency1->id
                        ],
                    ],
                    'goalUserAccount' => [
                        'id' => $this->userAccount2->id,
                        'currency' => [
                            'id' => $this->currency2->id
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testGetUserAccountByOtherUser()
    {
        $this->be($this->otherUser, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            {
                userAccountBuyTask(id: {$this->userAccountBuyTask1->id}) {
                    id
                    userAccount {
                        id
                        currency {
                            id
                        }
                    }
                    goalUserAccount {
                        id
                        currency {
                            id
                        }
                    }
                }
            }
        ")->assertGraphQLErrorMessage('This action is unauthorized.');
    }

    public function testGetUserAccountOutgoingBuyTasks()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            {
                userAccountOutgoingBuyTasks(user_account_id: {$this->userAccount1->id}) {
                    data {
                        id
                        userAccount {
                            id
                            currency {
                                id
                            }
                        }
                        goalUserAccount {
                            id
                            currency {
                                id
                            }
                        }
                    }
                }
            }
        ")->assertJson([
            'data' => [
                'userAccountOutgoingBuyTasks' => [
                    'data' => [
                        [
                            'id' => $this->userAccountBuyTask1->id,
                            'userAccount' => [
                                'id' => $this->userAccount1->id,
                                'currency' => [
                                    'id' => $this->currency1->id
                                ],
                            ],
                            'goalUserAccount' => [
                                'id' => $this->userAccount2->id,
                                'currency' => [
                                    'id' => $this->currency2->id
                                ],
                            ],
                        ],
                        [
                            'id' => $this->userAccountBuyTask2->id,
                            'userAccount' => [
                                'id' => $this->userAccount1->id,
                                'currency' => [
                                    'id' => $this->currency1->id
                                ],
                            ],
                            'goalUserAccount' => [
                                'id' => $this->userAccount2->id,
                                'currency' => [
                                    'id' => $this->currency2->id
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testGetUserAccountOutgoingBuyTasksByOtherUser()
    {
        $this->be($this->otherUser, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            {
                userAccountOutgoingBuyTasks(user_account_id: {$this->userAccount1->id}) {
                    data {
                        id
                        userAccount {
                            id
                            currency {
                                id
                            }
                        }
                        goalUserAccount {
                            id
                            currency {
                                id
                            }
                        }
                    }
                }
            }
        ")->assertGraphQLErrorMessage('This action is unauthorized.');
    }

    public function testGetUserAccountIncomingBuyTasks()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            {
                userAccountIncomingBuyTasks(goal_user_account_id: {$this->userAccount2->id}) {
                    data {
                        id
                        userAccount {
                            id
                            currency {
                                id
                            }
                        }
                        goalUserAccount {
                            id
                            currency {
                                id
                            }
                        }
                    }
                }
            }
        ")->assertJson([
            'data' => [
                'userAccountIncomingBuyTasks' => [
                    'data' => [
                        [
                            'id' => $this->userAccountBuyTask1->id,
                            'userAccount' => [
                                'id' => $this->userAccount1->id,
                                'currency' => [
                                    'id' => $this->currency1->id
                                ],
                            ],
                            'goalUserAccount' => [
                                'id' => $this->userAccount2->id,
                                'currency' => [
                                    'id' => $this->currency2->id
                                ],
                            ],
                        ],
                        [
                            'id' => $this->userAccountBuyTask2->id,
                            'userAccount' => [
                                'id' => $this->userAccount1->id,
                                'currency' => [
                                    'id' => $this->currency1->id
                                ],
                            ],
                            'goalUserAccount' => [
                                'id' => $this->userAccount2->id,
                                'currency' => [
                                    'id' => $this->currency2->id
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testGetUserAccountIncomingBuyTasksByOtherUser()
    {
        $this->be($this->otherUser, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            {
                userAccountIncomingBuyTasks(goal_user_account_id: {$this->userAccount2->id}) {
                    data {
                        id
                        userAccount {
                            id
                            currency {
                                id
                            }
                        }
                        goalUserAccount {
                            id
                            currency {
                                id
                            }
                        }
                    }
                }
            }
        ")->assertGraphQLErrorMessage('This action is unauthorized.');
    }

    public function testGetNotExistentUserAccountBuyTask()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            {
                userAccountBuyTask(id: 10000000) {
                    id
                }
            }
        ")->assertGraphQLErrorMessage('This action is unauthorized.');
    }

    public function testGetUserAccountOutgoingBuyTasksByNotExistentUserAccount()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            {
                userAccountOutgoingBuyTasks(user_account_id: 10000000) {
                    data {
                        id
                        userAccount {
                            id
                            currency {
                                id
                            }
                        }
                        goalUserAccount {
                            id
                            currency {
                                id
                            }
                        }
                    }
                }
            }
        ")->assertGraphQLErrorMessage('This action is unauthorized.');
    }

    public function testGetUserAccountIncomingBuyTasksByNotExistentUserAccount()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            {
                userAccountIncomingBuyTasks(goal_user_account_id: 10000000) {
                    data {
                        id
                        userAccount {
                            id
                            currency {
                                id
                            }
                        }
                        goalUserAccount {
                            id
                            currency {
                                id
                            }
                        }
                    }
                }
            }
        ")->assertGraphQLErrorMessage('This action is unauthorized.');
    }

    public function testCreateUserAccountBuyTask()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                createUserAccountBuyTask(input: {
                    user_account_id: {$this->userAccount1->id}
                    goal_user_account_id: {$this->userAccount2->id}
                    value: 10
                    count: 1
                }) {
                    userAccount {
                        id
                        currency {
                            id
                        }
                    }
                    goalUserAccount {
                        id
                        currency {
                            id
                        }
                    }
                }
            }
        ")->assertJson([
            'data' => [
                'createUserAccountBuyTask' => [
                    'userAccount' => [
                        'id' => $this->userAccount1->id,
                        'currency' => [
                            'id' => $this->currency1->id,
                        ],
                    ],
                    'goalUserAccount' => [
                        'id' => $this->userAccount2->id,
                        'currency' => [
                            'id' => $this->currency2->id,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testCreateUserAccountBuyTaskByOtherUser()
    {
        $this->be($this->otherUser, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                createUserAccountBuyTask(input: {
                    user_account_id: {$this->userAccount1->id}
                    goal_user_account_id: {$this->userAccount2->id}
                    value: 10
                    count: 1
                }) {
                    userAccount {
                        id
                        currency {
                            id
                        }
                    }
                    goalUserAccount {
                        id
                        currency {
                            id
                        }
                    }
                }
            }
        ")->assertGraphQLErrorMessage('This action is unauthorized.');
    }

    public function testCreateUserAccountBuyTaskOnNotExistentUserAccount()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                createUserAccountBuyTask(input: {
                    user_account_id: 10000000
                    goal_user_account_id: {$this->userAccount2->id}
                    value: 10
                    count: 1
                }) {
                    userAccount {
                        id
                        currency {
                            id
                        }
                    }
                    goalUserAccount {
                        id
                        currency {
                            id
                        }
                    }
                }
            }
        ")->assertGraphQLErrorMessage('Validation failed for the field [createUserAccountBuyTask].');
    }

    public function testCreateUserAccountBuyTaskOnNotExistentGoalUserAccount()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                createUserAccountBuyTask(input: {
                    user_account_id: {$this->userAccount1->id}
                    goal_user_account_id: 10000000
                    value: 10
                    count: 1
                }) {
                    userAccount {
                        id
                        currency {
                            id
                        }
                    }
                    goalUserAccount {
                        id
                        currency {
                            id
                        }
                    }
                }
            }
        ")->assertGraphQLErrorMessage('Validation failed for the field [createUserAccountBuyTask].');
    }

    public function testCreateUserAccountBuyTaskOnSameUserAccountCurrencies()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                createUserAccountBuyTask(input: {
                    user_account_id: {$this->userAccount2->id}
                    goal_user_account_id: {$this->userAccount3->id}
                    value: 10
                    count: 1
                }) {
                    userAccount {
                        id
                        currency {
                            id
                        }
                    }
                    goalUserAccount {
                        id
                        currency {
                            id
                        }
                    }
                }
            }
        ")->assertGraphQLErrorMessage('Validation failed for the field [createUserAccountBuyTask].');
    }

    public function testCreateUserAccountBuyTaskOnUserAccountWithArchivedCurrency()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                createUserAccountBuyTask(input: {
                    user_account_id: {$this->userAccountWithArchivedCurrency->id}
                    goal_user_account_id: {$this->userAccount3->id}
                    value: 10
                    count: 1
                }) {
                    userAccount {
                        id
                        currency {
                            id
                        }
                    }
                    goalUserAccount {
                        id
                        currency {
                            id
                        }
                    }
                }
            }
        ")->assertGraphQLErrorMessage('Validation failed for the field [createUserAccountBuyTask].');
    }

    public function testCreateUserAccountBuyTaskOnGoalUserAccountWithArchivedCurrency()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                createUserAccountBuyTask(input: {
                    user_account_id: {$this->userAccount1->id}
                    goal_user_account_id: {$this->userAccountWithArchivedCurrency->id}
                    value: 10
                    count: 1
                }) {
                    userAccount {
                        id
                        currency {
                            id
                        }
                    }
                    goalUserAccount {
                        id
                        currency {
                            id
                        }
                    }
                }
            }
        ")->assertGraphQLErrorMessage('Validation failed for the field [createUserAccountBuyTask].');
    }

    public function testDeleteUserAccountBuyTaskByOtherUser()
    {
        $this->be($this->otherUser, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                deleteUserAccountBuyTask(id: {$this->userAccountBuyTask1->id}) {
                    id
                }
            }
        ")->assertGraphQLErrorMessage('This action is unauthorized.');
    }

    public function testDeleteNotExistentUserAccountBuy()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                deleteUserAccountBuyTask(id: 10000000) {
                    id
                }
            }
        ")->assertGraphQLErrorMessage('Validation failed for the field [deleteUserAccountBuyTask].');
    }

    public function testDeleteCompletedUserAccountBuy()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                deleteUserAccountBuyTask(id: {$this->userAccountBuyTask2->id}) {
                    id
                }
            }
        ")->assertGraphQLErrorMessage('Validation failed for the field [deleteUserAccountBuyTask].');
    }

    public function testDeleteUserAccountBuy()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                deleteUserAccountBuyTask(id: {$this->userAccountBuyTask1->id}) {
                    id
                }
            }
        ")->assertJson([
            'data' => [
                'deleteUserAccountBuyTask' => [
                    'id' => $this->userAccountBuyTask1->id,
                ],
            ],
        ]);
    }
}
