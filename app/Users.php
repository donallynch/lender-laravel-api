<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Users extends Model
{
    /**
     * @param int $id
     * @return \Illuminate\Support\Collection
     */
    public static function lender($id)
    {
        /* Retrieve Lender */
        $lender = DB::table('users')
            ->where('id', $id)
            ->where('type', 'lender')
            ->limit(1)
            ->get();

        /* Ensure Lender exists */
        if (count($lender) === 0) {
            return [];
        }

        /* Retrieve Lenders Transactions */
        $lendersTransactions = Transactions::lendersInfo($lender[0]->id);

        /* Retrieve Lenders Bids */
        $bids = Bids::lendersBids($lender[0]->id);

        /* Prep Lender object for response */
        $lender[0]->full_name = "{$lender[0]->first_name} {$lender[0]->last_name}";
        $lender[0]->url = "http://linked/api/lender/{$lender[0]->id}";
        $lender[0]->available_cash_balance = $lendersTransactions['available_cash_balance'];
        $lender[0]->total_deposits = $lendersTransactions['totalDeposit'];
        $lender[0]->total_bids = $lendersTransactions['totalBids'];
        $lender[0]->principal_amount = $lendersTransactions['principalAmount'];
        $lender[0]->total_interest = $lendersTransactions['totalInterest'];
        $lender[0]->outstanding_principal = $bids['op'];
        $lender[0]->total_bids = $bids['total'];
        $lender[0]->total_fee_paid_to_platform = 0;
        $lender[0]->live_bids_on_loans = $bids['lenderBids'];

        return $lender;
    }
}
