<?php

namespace Tests\Integration\GraphQL;

use App\Models\User;
use App\Models\Currency;
use App\Models\UserAccount;

class UserAccountTest extends TestCase
{
    /**
     * @var User
     */
    private $otherUser;
    /**
     * @var Currency
     */
    private $currency;
    /**
     * @var Currency
     */
    private $archivedCurrency;
    /**
     * @var UserAccount
     */
    private $userAccount1;
    /**
     * @var UserAccount
     */
    private $userAccount2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->otherUser = User::factory()->create();
        $this->currency = Currency::factory()->create();
        $this->archivedCurrency = Currency::factory()->create([
            'archived' => true,
        ]);
        $this->userAccount1 = UserAccount::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $this->currency->id,
            'value' => 0,
        ]);
        $this->userAccount2 = UserAccount::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $this->currency->id,
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->otherUser = null;
        $this->currency = null;
        $this->archivedCurrency = null;
        $this->userAccount1 = null;
        $this->userAccount2 = null;
    }

    public function testGetUserAccount()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            {
                userAccount(id: {$this->userAccount1->id}) {
                    id
                    currency {
                        id
                    }
                }
            }
        ")->assertJson([
            'data' => [
                'userAccount' => [
                    'id' => $this->userAccount1->id,
                    'currency' => [
                        'id' => $this->currency->id
                    ],
                ],
            ],
        ]);
    }

    public function testGetUserAccountWithoutSettingArgs()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            {
                testUserAccount(id: {$this->userAccount1->id}) {
                    id
                    currency {
                        id
                    }
                }
            }
        ")->assertGraphQLErrorMessage('This action is unauthorized.');
    }

    public function testGetUserAccountByOtherUser()
    {
        $this->be($this->otherUser, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            {
                userAccount(id: {$this->userAccount1->id}) {
                    id
                    currency {
                        id
                    }
                }
            }
        ")->assertGraphQLErrorMessage('This action is unauthorized.');
    }

    public function testGetUserAccounts()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            {
                userAccounts(user_id: {$this->user->id}) {
                    data {
                        id
                        currency {
                            id
                        }
                    }
                }
            }
        ")->assertJson([
            'data' => [
                'userAccounts' => [
                    'data' => [
                        [
                            'id' => $this->userAccount1->id,
                            'currency' => [
                                'id' => $this->currency->id
                            ],
                        ],
                        [
                            'id' => $this->userAccount2->id,
                            'currency' => [
                                'id' => $this->currency->id
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testGetUserAccountsByOtherUser()
    {
        $this->be($this->otherUser, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            {
                userAccounts(user_id: {$this->user->id}) {
                    data {
                        id
                        currency {
                            id
                        }
                    }
                }
            }
        ")->assertGraphQLErrorMessage('This action is unauthorized.');
    }

    public function testGetNotExistentUserAccount()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            {
                userAccount(id: 10000000) {
                    id
                    currency {
                        id
                    }
                }
            }
        ")->assertGraphQLErrorMessage('This action is unauthorized.');
    }

    public function testGetUserAccountsByNotExistentUser()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            {
                userAccounts(user_id: 10000000) {
                    data {
                        id
                        currency {
                            id
                        }
                    }
                }
            }
        ")->assertGraphQLErrorMessage('This action is unauthorized.');
    }

    public function testCreateUserAccount()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                createUserAccount(input: {
                    user_id: {$this->user->id}
                    currency_id: {$this->currency->id}
                }) {
                    user {
                        id
                    }
                    currency {
                        id
                    }
                }
            }
        ")->assertJson([
            'data' => [
                'createUserAccount' => [
                    'user' => [
                        'id' => $this->user->id,
                    ],
                    'currency' => [
                        'id' => $this->currency->id
                    ],
                ],
            ],
        ]);
    }

    public function testCreateUserAccountByOtherUser()
    {
        $this->be($this->otherUser, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                createUserAccount(input: {
                    user_id: {$this->user->id}
                    currency_id: {$this->currency->id}
                }) {
                    user {
                        id
                    }
                    currency {
                        id
                    }
                }
            }
        ")->assertGraphQLErrorMessage('This action is unauthorized.');
    }

    public function testCreateUserAccountByArchivedCurrency()
    {
        $this->be($this->otherUser, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                createUserAccount(input: {
                    user_id: {$this->user->id}
                    currency_id: {$this->archivedCurrency->id}
                }) {
                    user {
                        id
                    }
                    currency {
                        id
                    }
                }
            }
        ")->assertGraphQLErrorMessage('Validation failed for the field [createUserAccount].');
    }

    public function testCreateUserAccountWithNotExistenData()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                createUserAccount(input: {
                    user_id: 10000000
                    currency_id: {$this->currency->id}
                }) {
                    user {
                        id
                    }
                    currency {
                        id
                    }
                }
            }
        ")->assertGraphQLErrorMessage('Validation failed for the field [createUserAccount].');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                createUserAccount(input: {
                    user_id: {$this->user->id}
                    currency_id: 10000000
                }) {
                    user {
                        id
                    }
                    currency {
                        id
                    }
                }
            }
        ")->assertGraphQLErrorMessage('Validation failed for the field [createUserAccount].');
    }

    public function testTopUpUserAccount()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                topUpUserAccount(input: {
                    id: {$this->userAccount1->id}
                    value: 10
                }) {
                    id
                    value
                }
            }
        ")->assertJson([
            'data' => [
                'topUpUserAccount' => [
                    'id' => $this->userAccount1->id,
                    'value' => 10,
                ],
            ],
        ]);
    }

    public function testTopUpUserAccountByNegativeValue()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                topUpUserAccount(input: {
                    id: {$this->userAccount1->id}
                    value: -10
                }) {
                    id
                    value
                }
            }
        ")->assertGraphQLErrorMessage('Validation failed for the field [topUpUserAccount].');
    }

    public function testTopUpUserAccountByOtherUser()
    {
        $this->be($this->otherUser, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                topUpUserAccount(input: {
                    id: {$this->userAccount1->id}
                    value: 10
                }) {
                    id
                    value
                }
            }
        ")->assertGraphQLErrorMessage('This action is unauthorized.');
    }

    public function testTopUpNotExistentUserAccount()
    {
        $this->be($this->user, 'api');

        $this->graphQL(/** @lang GraphQL */ "
            mutation {
                topUpUserAccount(input: {
                    id: 10000000
                    value: 10
                }) {
                    id
                    value
                }
            }
        ")->assertGraphQLErrorMessage('Validation failed for the field [topUpUserAccount].');
    }
}
