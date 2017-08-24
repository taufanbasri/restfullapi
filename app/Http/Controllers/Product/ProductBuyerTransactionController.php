<?php

namespace App\Http\Controllers\Product;

use App\Product;
use App\User;
use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends ApiController
{
    public function store(Request $request, Product $product, User $buyer)
    {
        $rules = [
            'quantity' => 'required|integer|min:1'
        ];

        $this->validate($request, $rules);

        if ($buyer->id == $product->seller_id){
            return $this->errorResponse('The buyer must be different from the seller', 409);
        }

        if (!$buyer->isVerified()){
            return $this->errorResponse('The buyer is must be a verified user.', 409);
        }

        if (!$product->seller->isVerified()){
            return $this->errorResponse('The seller is must be a verified user.', 409);
        }

        if (!$product->isAvailable()){
            return $this->errorResponse('The product is not available.', 409);
        }

        if ($product->quantity < $request->quantity){
            return $this->errorResponse('The product does not have enough units for this transaction.', 409);
        }

        return DB::transaction(function () use ($request, $product, $buyer){
            $product->quantity -= $request->quantity;
            $product->save();

            $transactions = Transaction::create([
                'quantity' => $request->quantity,
                'buyer_id' => $buyer->id,
                'product_id' => $product->id,
            ]);

            return $this->showOne($transactions, 201);
        });
    }
}
