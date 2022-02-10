# creativeco-test

## Getting Started

1. Install dependencies:

```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php80-composer:latest \
    composer install --ignore-platform-reqs
```

2. Copy **.env.example** to **.env** and configure database connection with variables:
* `DB_CONNECTION`
* `DB_HOST`
* `DB_PORT`
* `DB_DATABASE`
* `DB_USERNAME`
* `DB_PASSWORD`

*You can leave default values, but* `DB_PASSWORD` *is **required**.*

3. Run containers:

```
./vendor/bin/sail up
```

4. Go to the app container shell:

```
./vendor/bin/sail shell
```

5. Generate app key:

```
php artisan key:generate
```

6. Run migations:

```
php artisan migrate
```

7. Setup Laravel passport:

```
php artisan passport:install
```

*After command competed get second client* `CLIENT_ID` *and* `CLIENT_SECRET` *and paste them to* **.env** *for variable* `PASSPORT_CLIENT_ID` *and* `PASSPORT_CLIENT_SECRET`.

8. Parse traded currencies:

```
php artisan parse:currencies
```

*It's important because scheduled currencies parse run at **00:00**.*

9. Go to playground: `0.0.0.0/graphql-playground`

10. Start with register mutation:

```
mutation {
  register(input: {
    name: "your_name"
    email: "your_email"
    password: "your_password"
    password_confirmation: "your_password"
  }) {
    tokens {
      access_token
      refresh_token
    }
  }
}
```

*Don't forget get* **tokens {access_token}**.

**For other queries and mutations this token need paste to HTTP header like**:

```
{
  "authorization": "Bearer your_access_token"
}
```

## GraphQL schema types

1. `User`:

| Attribute         | Type            | Additional description |
|-------------------|-----------------|------------------------|
| id                | ID!             |                        |
| name              | String!         |                        |
| email             | String!         |                        |
| email_verified_at | DateTime        |                        |
| created_at        | DateTime!       |                        |
| updated_at        | DateTime!       |                        |
| userAccounts      | [UserAccount!]! |                        |

2. `Currency`:

| Attribute            | Type                     | Additional description    |
|----------------------|--------------------------|---------------------------|
| id                   | ID!                      |                           |
| name                 | String!                  |                           |
| code                 | String!                  |                           |
| archived             | Boolean!                 | Currency no longer traded |
| created_at           | DateTime!                |                           |
| updated_at           | DateTime!                |                           |
| exchangeRates        | [CurrencyExchangeRate!]! |                           |
| exchangeRatesReverse | [CurrencyExchangeRate!]! |                           |

3. `CurrencyExchangeRate`:

| Attribute    | Type      | Additional description    |
|--------------|-----------|---------------------------|
| id           | ID!       |                           |
| value        | String!   |                           |
| created_at   | DateTime! |                           |
| updated_at   | DateTime! |                           |
| fromCurrency | Currency! |                           |
| toCurrency   | Currency! |                           |

4. `UserAccount`:

| Attribute        | Type                   | Additional description      |
|------------------|------------------------|-----------------------------|
| id               | ID!                    |                             |
| value            | Float!                 | Count of currency           |
| created_at       | DateTime!              |                             |
| updated_at       | DateTime!              |                             |
| user             | User!                  |                             |
| currency         | Currency!              |                             |
| outgoingBuyTasks | [UserAccountBuyTask!]! | Buy tasks from this account |
| incomingBuyTasks | [UserAccountBuyTask!]! | Buy tasks to this account   |

5. `UserAccountBuyTask`:

| Attribute       | Type         | Additional description                                         |
|-----------------|--------------|----------------------------------------------------------------|
| id              | ID!          |                                                                |
| value           | Float!       | Max value of curerncies exchange rate                          |
| count           | Float!       | Count of purchased currency                                    |
| created_at      | DateTime!    |                                                                |
| buy_before      | DateTime     | After this time purchase will not to be attempt to carried out |
| completed_at    | DateTime     |                                                                |
| canceled_at     | DateTime     | Time when is one of accounts currency became archived          |
| userAccount     | UserAccount! | Account from which currency will be debited                    |
| goalUserAccount | UserAccount! | Account to which currency will be received                     |

## GraphQL schema queries and mutations

1. `Queries`:

| Name                | Description                                | Arguments            | Return                 |
|---------------------|--------------------------------------------|----------------------|------------------------|
| me                  | Get the current authenticated user         |                      | User                   |
| tradedCurrencies    | Get all current traded currencies          |                      | [Currency!]!           |
| userAccount         | Get the user account                       | id: ID!              | UserAccount            |
| userAccounts        | Get all user accounts                      | user_id: ID!         | [UserAccount!]!        |
| userAccountBuyTask  | Get the currency buy task                  | id: ID!              | UserAccountBuyTask     |
| userAccountBuyTasks | Get all currency buy tasks by user account | user_account_id: ID! | [UserAccountBuyTask!]! |

2. `Mutations`:

| Name                     | Description                                               | Arguments                                                                              | Return             |
|--------------------------|-----------------------------------------------------------|---------------------------------------------------------------------------------------|--------------------|
| createUserAccount        | Create user account for buy tasks                         | input {<br/>&nbsp;&nbsp;&nbsp;&nbsp;user_id: ID!<br/>&nbsp;&nbsp;&nbsp;&nbsp;currency_id: ID!<br/>}                                                                         | UserAccount        |
| topUpUserAccount         | Top up user account                                       | input {<br/>&nbsp;&nbsp;&nbsp;&nbsp;id: ID!<br/>&nbsp;&nbsp;&nbsp;&nbsp;value: Float!<br/>}                                                                      | UserAccount        |
| createUserAccountBuyTask | Create task for buying currency from one account to other | input {<br/>&nbsp;&nbsp;&nbsp;&nbsp;user_account_id: ID!<br/>&nbsp;&nbsp;&nbsp;&nbsp;goal_user_account_id: ID!<br/>&nbsp;&nbsp;&nbsp;&nbsp;value: Float!<br/>&nbsp;&nbsp;&nbsp;&nbsp;count: Float!<br/>&nbsp;&nbsp;&nbsp;&nbsp;buy_before: DateTime<br>} | UserAccountBuyTask |
| deleteUserAccountBuyTask | Delete waiting buy task                                   | id: ID!                                                                          | UserAccountBuyTask |
