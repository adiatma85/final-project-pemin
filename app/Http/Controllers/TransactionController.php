<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Book;
use App\Http\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class TransactionController extends Controller
{

    use ResponseTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // Index Transactions
    public function index(Request $request)
    {
        try {
            $user = $request->user;
            $transactions = $user->role == 'admin' ? Transaction::with(['user', 'book'])->get()
                : Transaction::where('user_id', $user->id)->with(['book', 'user'])->get();

            return $this->response(true, 'success fetch resources', compact('transactions'), Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->response(false, 'error in transaction index func', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Get By Id Transaction
    public function getById(Request $request, $transactionId)
    {
        try {
            $transaction = Transaction::where('id', $transactionId)
                // ->where('user_id', $request->user->id)
                ;

            if ($transaction->exists()) {
                $condition = $request->user->hasRole('admin');
                $transaction = $condition ? $transaction->with(['user', 'book'])->first() : $transaction->with(['book'])->first();
                if ($condition || $request->user->id == $transaction->user->id) {
                    return $this->response(true, 'success to fetch particular resources', compact('transaction'), Response::HTTP_OK);
                }

                // Else
                return $this->response(false, 'forbidden request', null, Response::HTTP_FORBIDDEN);
            } else {
                return $this->response(false, 'failed to fetch particular resources', null, Response::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            return $this->response(false, 'error in transaction getById func', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Store
    public function store(Request $request)
    {

        // Validate the request
        $validator = Validator::make($request->all(), [
            'book_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->response(false, $validator->errors(), null, Response::HTTP_BAD_REQUEST);
        }



        try {
            $book = Book::find($request->book_id);
            if ($book) {
                $newTransaction = Transaction::create([
                    'user_id' => $request->user->id,
                    'book_id' => $book->id,
                    'deadline' => Carbon::now()->addWeek()->format('Y-m-d H:i:s'),
                ]);

                if ($newTransaction) {
                    return $this->response(true, 'success create new resouece', [
                        'transaction' => Transaction::where('id', $newTransaction->id)->with(['book', 'user'])->first(),
                    ], Response::HTTP_CREATED);
                } else {
                    return $this->response(false, 'failed to create new resource', null, Response::HTTP_BAD_REQUEST);;
                }
            } else {
                return $this->response(false, 'failed to fetch particular resources', null, Response::HTTP_BAD_REQUEST);
            }
        } catch (\Throwable $th) {
            return $this->response(false, 'error in transaction store func', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Update
    public function update(Request $request, $transactionId)
    {
        try {
            $transaction = Transaction::where('id', $transactionId);
            if ($transaction->exists()) {
                $transaction->update($request->only('deadline'));
                return $this->response(true, 'success to update partocilar resource', [ 'transaction' => $transaction->with(['book', 'user'])->first() ], Response::HTTP_OK);
            } else {
                return $this->response(false, 'failed to fetch particular resources', null, Response::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            return $this->response(false, 'error in transaction update func', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
