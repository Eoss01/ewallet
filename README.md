# Wallet System

This is a Laravel-based wallet system that supports deposit, withdrawal, and rebate functionalities, while safely handling concurrent operations on the same wallet.

## 1. Overview
- Users can deposit and withdraw funds, and view wallet balance and transaction history.  
- Deposits automatically trigger a **1% rebate**, processed asynchronously via Laravel Queue Jobs.  
- Supports high-concurrency operations to ensure wallet balances remain accurate.

## 2. Installation & Setup

### Clone the repository
git clone https://github.com/Eoss01/ewallet.git
cd ewallet
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
php artisan queue:work

## 3. API Endpoints
| Method | URL                             | Description                                                               |
| ------ | ------------------------------- | ------------------------------------------------------------------------- |
| GET    | /wallets/{wallet}/show\_balance | Retrieve wallet balance                                                   |
| GET    | /wallets/{wallet}/transactions  | Retrieve transaction history (optional `from_date` and `to_date` filters) |
| POST   | /wallets/{wallet}/deposit       | Deposit funds, payload: `{ "amount": 100 }`                               |
| POST   | /wallets/{wallet}/withdraw      | Withdraw funds, payload: `{ "amount": 50 }`                               |

Deposits automatically trigger a 1% rebate, processed asynchronously via Queue Jobs.

## 4. Concurrency Handling

Lock Mechanism:
When updating wallet balances, pessimistic locking is used (DB::transaction() + $wallet->lockForUpdate()) to ensure consistency when multiple deposits, withdrawals, or rebates occur simultaneously.

Queue Jobs:
Rebates are processed asynchronously via RebateJob, which also uses locks to guarantee correctness.

Testing Validation:
Unit tests cover single deposits, withdrawals, and transaction queries.
Concurrent deposits and withdrawals are tested to verify wallet balances and transaction counts remain accurate.

## 5. Testing

Run unit tests
php artisan test

Run parallel tests
php artisan test --parallel

Test coverage includes:

Single deposit + rebate
Single withdrawal
Transaction history queries
Multiple concurrent deposits and withdrawals (concurrentDepositAndWithdraw)
