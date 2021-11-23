<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
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

    // Index
    public function index()
    {
        try {
            $books = Book::all();
            return $this->response(true, 'success fetching resources', compact('books'), Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->response(false, 'internal server error at index book func', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Store
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'author' =>  'required|string',
            'year' => 'required|numeric',
            'synopsis' => 'required|string',
            'stock' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response(false, $validator->errors(), null, Response::HTTP_BAD_REQUEST);
        }

        try {
            $book = Book::create($request->all());

            if ($book) {
                return $this->response(true, 'success create resource', compact('book'), Response::HTTP_CREATED);
            } else {
                return $this->response(false, 'failed to create new resource', null, Response::HTTP_BAD_REQUEST);
            }
        } catch (\Throwable $th) {
            return $this->response(false, 'internal server error at store book func', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Show / Get Book by ID
    public function getById($bookId)
    {
        try {
            $book = Book::find($bookId);
            if ($book) {
                return $this->response(true, 'success to fetch praticular resource', compact('book'), Response::HTTP_OK);
            } else {
                return $this->response(false, 'failed to search particular resource', null, Response::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            return $this->response(false, 'internal server error at show book func', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Update
    public function update(Request $request, $bookId)
    {
        try {
            $book = Book::where('id', $bookId);
            if ($book->exists()) {
                $book->update($request->all());
                return $this->response(true, 'success to update a resource', ['book' => $book->first()], Response::HTTP_OK);
            } else {
                return $this->response(false, 'particular resopnse does not found', null, Response::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            return $this->response(false, 'internal server error at update book func', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Delete
    public function delete($bookId)
    {
        try {
            $book = Book::where('id', $bookId);
            if ($book->exists()) {
                $book->delete();
                return $this->response(true, 'success to delete a resource', null, Response::HTTP_OK);
            } else {
                return $this->response(false, 'particular resource does not found', null, Response::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            return $this->response(false, 'internal server error at delete book func', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
