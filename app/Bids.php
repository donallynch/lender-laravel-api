<?php

namespace App;

use \App\Users;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Bids
 * @package App
 */
class Bids extends Model
{
    /**
     * Define fields that are allowed to be used during Create/Update operations
     * @var array
     */
    protected $fillable = ['lender_id','loan_id','amount'];

    /** @var bool $timestamps Does eloquent force (created_at, updated_at etc ) auto timestamps, true or false */
    public $timestamps = false;

    /**
     * Retrieve all Bids for the specified Lender
     * @param int $id
     * @return mixed
     */
    public static function lendersBids($id)
    {
        /* Retrieve Live Loans */
        $bids = DB::table('bids')
            ->where('lender_id', $id)
            ->get();

        $total = 0;
        $op = 0;
        foreach ($bids as $key => $bid) {
            $total += $bid->amount;
            $op += $bid->outstanding_principal;
        }

        return [
            'total' => $total,
            'op' => $op,
            'lenderBids' => $bids
        ];
    }

    /**
     * Create Bid:
     *  1. Retrieve Lender, Ensure Lender Exists
     *  2. Retrieve Lender's available cash balance
     *  3. Retrieve Loan, Ensure Loan Exists
     *  4. Ensure Lender has enough cash balance to place requested bid
     *  5. Determine refund amount (if any)
     *  6. Create/insert new Bid, Ensure new Bid was created
     *  7. Create associated Refund Transaction (if required)
     *  8. Create associated Transaction
     *
     * TODO:: Relocate each external model function to the respective model, and just call it from here; For example Users, Loans and Transactions model functions should not be in here but deadline approaching.
     *
     * @param $post
     * @return array
     */
    public static function post($post)
    {
        $now = date('Y-m-d H:i:s');

        /* Retrieve Lender */
        $lender = DB::table('users')
            ->where('id', $post['lender_id'])
            ->where('type', 'lender')
            ->limit(1)
            ->get();
        
        /* Ensure Lender Exists */
        if (count($lender) === 0) {
            return ['status' => 404, 'message' => 'lender not found'];
        }

        /* Retrieve Lender's available cash balance */
        $transactions = Transactions::lendersTransactions($post['lender_id']);
        $lendersBalance = Transactions::buildLenderData($transactions)['available_cash_balance'];

        /* Retrieve Loan */
        $loan = DB::table('loans')
            ->where('id', $post['loan_id'])
            ->limit(1)
            ->get();

        /* Ensure Loan Exists */
        if (count($loan) === 0) {
            return ['status' => 404, 'message' => 'loan not found'];
        }

        /* Ensure Lender has enough cash balance to place requested bid */
        if ($lendersBalance - $post['amount'] < 0) {
            return ['status' => 400, 'message' => 'not enough balance to place bid'];
        }

        /* Determine refund amount (if any) */
        $refundAmount = 0;
        if ($post['amount'] > $loan[0]->amount) {
            $refundAmount = $post['amount'] = $loan[0]->amount;
        }

        /* Create/insert new Bid */
        $newBidId = DB::table('bids')->insertGetId([
            'loan_id' => $post['loan_id'],
            'lender_id' => $post['lender_id'],
            'amount' => $post['amount'],
            'repayment_amount' => 0,
            'outstanding_principal' => 0,
        ]);

        /* Ensure new Bid was created */
        if ((int)$newBidId === 0) {
            return ['status' => 500, 'message' => 'An error occurred which prevented the Bid from being created. Please try again later'];
        }

        /* If there is a refund amount */
        $newRefundId = 0;
        if ($refundAmount > 0) {
            /* Create associated Refund Transaction */
            $newRefundId = DB::table('transactions')->insertGetId([
                'user_id' => $post['lender_id'],
                'type' => 'refund',
                'amount' => $refundAmount,
                'interest' => 0,
                'fee' => 0,
                'bid_id' => 0,
                'repayment_id' => 0,
                'date' => $now,
            ]);
        }

        /* Create associated Transaction */
        DB::table('transactions')->insertGetId([
            'user_id' => $post['lender_id'],
            'type' => 'bid',
            'amount' => $post['amount'],
            'interest' => 0,
            'fee' => 0,
            'bid_id' => $newBidId,
            'repayment_id' => $newRefundId,
            'date' => $now,
        ]);

        return ['status' => 201];
    }
}

