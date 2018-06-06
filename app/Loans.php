<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use \App\Users;

class Loans extends Model
{
    /**
     * @param array|mixed $page
     * @param string $sortField
     * @param string $sortOrder
     * @return array
     */
    public static function live($page, $sortField, $sortOrder)
    {
        /* Retrieve Live Loans */
        $loans = DB::table('loans')
            ->where('is_approved', 1)
            //->where('is_accepted', 0)// Not sure if this is needed and would enquire with the project manager (if one existed) to elaborate.
            ->limit(5)
            ->offset(($page-1)*5)
            ->orderBy($sortField, $sortOrder)
            ->get();

        /* Ensure Loans exist */
        if (count($loans) === 0) {
            return [];
        }

        /* Attach one to ones entities to collection */
        foreach ($loans as $loan) {

            /* Each Loan has a borrower (user) */
            $loan->borrower = DB::table('users')
                ->where('id', $loan->borrower_id)
                ->get();

            $loan->borrower[0]->url = "http://linked/api/lender/{$loan->borrower_id}";

            /* Borrower Field Prep */
            $loan->business_name = $loan->business;
            $loan->loan_description = $loan->description;
            $loan->requested_amount = $loan->amount;
            $loan->loan_term = "{$loan->term} months";
            $loan->borrower[0]->full_name = "{$loan->borrower[0]->first_name} {$loan->borrower[0]->last_name}";
            $loan->url = "http://linked/api/loan/{$loan->id}";
            $loan->collection_url = "http://linked/api/loans/live?page=1";

            /* Each Loan has multiple accepted bids */
            $loan->accepted_bids = DB::table('bids')
                ->where('loan_id', $loan->id)
                ->get();

            /* Bid field prep */
            $loan->accepted_bids_total = 0;
            foreach ($loan->accepted_bids as $bid) {
                $bid->url = "http://linked/api/bid/{$bid->id}";
                $bid->collection_url = "http://linked/api/bids?page=1";
                $loan->accepted_bids_total += $bid->amount;
            }
        }

        return $loans;
    }
}
