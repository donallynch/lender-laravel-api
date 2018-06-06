<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Transactions
 * @package App
 */
class Transactions extends Model
{
    /**
     * @param int $lenderId
     * @return \Illuminate\Support\Collection
     */
    public static function lendersTransactions($lenderId)
    {
        /* Retrieve Live Loans */
        return DB::table('transactions')
            ->where('user_id', $lenderId)
            ->get();
    }

    /**
     * @param int $id
     * @return array
     */
    public static function lendersInfo($id)
    {
        $lendersTransactions = \App\Transactions::lendersTransactions($id);

        return self::buildLenderData($lendersTransactions);
    }

    /**
     * @param $transactions
     * @return array
     */
    public static function buildLenderData($transactions)
    {
        /* Define transaction types */
        $transactionTypes = [
            'deposit' => 1,
            'repayment' => 1,
            'refund' => 1
        ];

        /* Build Lender data */
        $total = 0;
        $openBids = 0;
        $deposit = 0;
        $bids = 0;
        $repayments = 0;
        $repaymentInterest = 0;
        foreach ($transactions as $key => $value) {
            if ($value->type === 'deposit') {
                $deposit += $value->amount;
            } elseif ($value->type === 'bid') {
                $bids += $value->amount;
            } elseif ($value->type === 'repayment') {
                $repayments += $value->amount;
                $repaymentInterest += $value->interest;
            }

            if (array_key_exists($value->type, $transactionTypes)) {
                $total += $value->amount;
            } elseif ($value->type === 'bid') {
                $openBids += $value->amount;
            }
        }

        /* Return Lender data */
        return [
            'available_cash_balance' => round($total - $openBids, 1),
            'totalDeposit' => round($deposit, 2),
            'totalBids' => $bids,
            'principalAmount' => round($repayments - $repaymentInterest, 2),
            'totalInterest' => round($repaymentInterest, 2)
        ];
    }
}
