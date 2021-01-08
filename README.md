# Account Management Backend - Level 3

**Before you get started, please read [this guide](https://www.notion.so/Get-started-with-your-assignment-dade100d93054a6db1036ce294bdaeb6)** that walks you through how to submit your solution and get help.

### Time limit ⏳

Try not to spend more than **3 hours**.

### The challenge 🎯

Your task is to build a backend service that implements this [API specification](api-specification.yml) that defines a set of operations for creating and reading account transactions. You can use [editor.swagger.io](https://editor.swagger.io/) to visualize the spec.

### The focus areas 🔍
- **Use an SQLite database as the service datastore.** We want to see how you design the database schema and SQL queries for working with the service data. Please use [SQLite](https://www.sqlite.org/index.html) as a DB engine.
- **Create a backend service that implements the provided API.** This will involve the following:
  - Handling invalid HTTP requests;
  - Creating new transactions idempotently;
  - Reading historical transactions;
  - Retreiving the current account balance.
  - Fetching information about the maximum transaction volume.
- **Optimize the GET endpoints for speed.** When designing your service, ensure that the GET endpoints remain fast with the database growing in size.
- **Ensure no lost updates.** When submitting a new transaction, make sure no account balance updates are lost. E.g., when having two concurrent requests updating the same account balance.
- **Minimize the number of SQL queries for fetching max transaction volume.** Try to do it with ideally a single SQL query.
- **Organize your code as a set of low-coupled modules**. Avoid duplication and extract re-usable modules where it makes sense, but don't break things apart needlessly. We want to see that you can create a codebase that is easy to maintain.
- **Document your decisions.** Extend this README.md with info about how to run your application along with any hints that will help us review your submission and better understand the decisions you made.

### The provided boilerplate 🗂
* The [service specification](api-specification.yml) in the Open API format.
* Automated tests to validate your solution. To run locally:
  * Install the required test dependencies with `yarn install`.
  * Update the `apiUrl` (where your app will run) in [cypress.json](cypress.json).
  * Run your app.
  * Run the tests with `yarn run test`.

### Before submitting your solution ⚠️
1. Update the `apiUrl` (where your app will run) in [cypress.json](cypress.json).
2. Update the [`build`](package.json#L5) and [`start`](package.json#L6) scripts in [package.json](package.json) that respectively build and run your application. **[See examples](https://www.notion.so/devskills/Backend-78f49bea524148228f29ceb446157474)**.

---

Made by [DevSkills](https://devskills.co). 

How was your experience? **Give us a shout on [Twitter](https://twitter.com/DevSkillsHQ) / [LinkedIn](https://www.linkedin.com/company/devskills)**.


## Notes on the solution

### One or two tables

I did some plays with the DB.

My first thought was to have only `transactions` table avoiding `accounts` table. I wrote some code for counting
sum per account and max transactions numbers. Just for an experiment. This would work, but I suspect would be slow
at large DB. So I had to create `accounts` table as well.

### Store transactions number to `accounts` table

Your task to `Minimize the number of SQL queries for fetching max transaction volume.` at first make me thought I had to
fetch the data from `transactions` table. But then I arrived at a decsision to store both, `account total amount` and 
`number of transactions`, in `accounts` table. This allowed to avoid a heavy nested query to get everything from `transactions`.
And since I update `accounts` table anyway with the total amount, I also update it with the transactions number which is
much quicker to get later.

### Minimizing `accounts` create or update query number

I still had to play with Eloquent to minimize the number of queries when creating/updating `accounts`. Native convinient 
`Model::increment()` method doesn't allow to increment 2 fields at once (`amount` and `transaction` number). 
And calling it twice generates 2 queries. So I had to implement like you can see here 
[TransactionService](laravel/app/Service/TransactionService.php#L19)

### Minimize the number of SQL queries for fetching max transaction volume

I didn't manage to find a way to use one query without a subquery.
There is a solution to get the first row with an aggregated column,
but we don't know how many accounts with max number of transactions we have.

Something like `use max in where` doesn't seem to be possible without
a subquery in SQL.

After many tries (to have formally one query) I used a `raw` Eloquent query 
with a `raw` subquery approach. [AccountService](laravel/app/Service/AccountService.php#L30)0

I'd better prefer to use 2 queries to avoid Eloqeunt raw queries (JIC to stay DB agnostic).
Smth. like

```php
$max = Account::max('transaction'); // returns int
$items = Account::where('transaction', $max);
```
But the requirement of one query made me to follow an ugglier way (at my point of view).

### Ensure no lost updates

I thought of `pessimistic locking` or queue. I tried `DB::transaction` ,`begin/commit/rollback` but then did several tries
using simultaneous `curl` requests and realized that the model save method would not allow to save a second transaction
with the same id due to the DB constraints (primary unique). So I just catch the `QueryException` and check it's code.
See [TransactionService](laravel/app/Service/TransactionService.php#L28).

### Header

According to the swagger, you almost always demand to return not JSON body with a message, but HTTP `Description` header.
So I did handled it a little to return both JIC.