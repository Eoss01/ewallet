<?php

namespace Tests\Feature;

use App\Enums\TransactionType;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Carbon\Carbon;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Jobs\RebateJob;
use App\Models\Setting;
use PHPUnit\Framework\Attributes\Test;

class WalletFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected Wallet $wallet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->wallet = Wallet::factory()->create([
            'wallet_balance' => 0,
        ]);
    }

    #[Test]
    public function showWalletBalance()
    {
        // 測試可以拿到錢包餘額
        $response = $this->getJson(route('wallets.balance', ['wallet' => $this->wallet->cid]));

        $response->assertOk()->assertJson(['wallet_cid' => $this->wallet->cid, 'balance' => 0]);
    }

    #[Test]
    public function showTransactionList()
    {
        // 測試可以拿到交易紀錄

        // 今天的交易
        Transaction::factory()->create([
            'wallet_id' => $this->wallet->id,
            'transaction_type' => TransactionType::Deposit,
            'transaction_date' => Carbon::today()->toDateString(),
            'transaction_amount' => 100,
        ]);

        // 五天前的交易
        Transaction::factory()->create([
            'wallet_id' => $this->wallet->id,
            'transaction_type' => TransactionType::Withdrawal,
            'transaction_date' => Carbon::today()->subDays(5)->toDateString(),
            'transaction_amount' => 50,
        ]);

        // 只拿到今天的交易
        $response = $this->getJson(route('wallets.transactions', [
            'wallet' => $this->wallet->cid,
            'from_date' => Carbon::today()->toDateString(),
            'to_date'   => Carbon::today()->toDateString(),
        ]));

        $response->assertOk();
        $transactions = $response->json('transactions');

        // 驗證只拿到今天的交易
        $this->assertCount(1, $transactions);
        $this->assertEquals(100, $transactions[0]['transaction_amount']);

        // 拿完所有交易
        $response2 = $this->getJson(route('wallets.transactions', [
            'wallet' => $this->wallet->cid,
        ]));

        $response2->assertOk();
        $transactions2 = $response2->json('transactions');

        // 驗證拿完所有交易
        $this->assertCount(2, $transactions2);
        $this->assertEquals(100, $transactions2[0]['transaction_amount']); // 今天的
        $this->assertEquals(50, $transactions2[1]['transaction_amount']); // 五天前的
    }

    #[Test]
    public function postDepositRebate()
    {
        // 測試可以存錢並執行rebate
        Queue::fake();

        $setting = Setting::factory()->create([
            'rebate_percent' => 1,
        ]);

        $response = $this->postJson(route('wallets.deposit', ['wallet' => $this->wallet->cid]), [
            'amount' => 100,
        ]);

        $response->assertStatus(201)->assertJson(['message' => 'Deposit successful. Rebate will be processed asynchronously.']);

        // 確認有執行rebate job
        Queue::assertPushed(RebateJob::class);

        // 執行rebate job
        $depositTx = Transaction::where('transaction_type', TransactionType::Deposit)->first();
        (new RebateJob($depositTx, $setting))->handle();

        // 驗證餘額 = 100 + 1%
        $amount = 100 + (100 * ($setting->rebate_percent / 100));

        $this->assertEquals($amount, $this->wallet->fresh()->wallet_balance);
        // 驗證有兩個交易
        $this->assertCount(2, $this->wallet->transactions);
        $this->assertEquals(TransactionType::Deposit, $this->wallet->transactions->first()->transaction_type);
        $this->assertEquals(TransactionType::Rebate, $this->wallet->transactions->last()->transaction_type);
    }

    #[Test]
    public function postWithdraw()
    {
        // 測試可以提錢
        $this->wallet->update(['wallet_balance' => 500]);

        $response = $this->postJson(route('wallets.withdraw', ['wallet' => $this->wallet->cid]), [
            'amount' => 200,
        ]);

        $response->assertStatus(201)->assertJson(['message' => 'Withdrawal successful.']);

        $this->assertEquals(300, $this->wallet->fresh()->wallet_balance);
        // 驗證有一個交易
        $this->assertCount(1, $this->wallet->transactions);
        $this->assertEquals(TransactionType::Withdrawal, $this->wallet->transactions->first()->transaction_type);
    }

    #[Test]
    public function concurrentDepositAndWithdraw()
    {
        Queue::fake();

        $setting = Setting::factory()->create(['rebate_percent' => 1]);

        $depositAmount = 100;
        $withdrawAmount = 50;
        $depositCount = 5;
        $withdrawCount = 3;

        // 模擬多筆存款
        for ($i = 0; $i < $depositCount; $i++)
        {
            $this->postJson(route('wallets.deposit', ['wallet' => $this->wallet->cid]), [
                'amount' => $depositAmount,
            ]);
        }

        // 模擬多筆提款
        for ($i = 0; $i < $withdrawCount; $i++)
        {
            $this->postJson(route('wallets.withdraw', ['wallet' => $this->wallet->cid]), [
                'amount' => $withdrawAmount,
            ]);
        }

        // 執行所有 Deposit 的 RebateJob
        $depositTxs = Transaction::where('transaction_type', TransactionType::Deposit)->get();
        foreach ($depositTxs as $depositTx)
        {
            (new RebateJob($depositTx, $setting))->handle();
        }

        // 計算預期錢包餘額
        // Deposit: 5次 * 100 = 500
        // Rebate: 500 * 1% = 5
        // Withdraw: 3次 * 50 = 150
        $expectedBalance = (5 * $depositAmount) + (5 * $depositAmount * ($setting->rebate_percent / 100)) - (3 * $withdrawAmount);

        $this->assertEquals($expectedBalance, $this->wallet->fresh()->wallet_balance);

        // 驗證交易數量
        $transactions = $this->wallet->transactions;
        // 5 deposit + 5 rebate + 3 withdraw = 13
        $this->assertCount($depositCount + $depositCount + $withdrawCount, $transactions);
    }
}
