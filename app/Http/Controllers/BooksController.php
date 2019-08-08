<?php

namespace App\Http\Controllers;

use App\Books;
use Illuminate\Http\Request;
use JWTAuth;

class BooksController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
        return $this->user
            ->books()
            ->get(['name', 'price', 'quantity'])
            ->toArray();
    }

    public function show($id)
    {
        $book = $this->user->books()->find($id);
    
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, book with id ' . $id . ' cannot be found'
            ], 400);
        }
    
        return $book;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'price' => 'required|integer',
            'quantity' => 'required|integer'
        ]);
    
        $book = new Books();
        $book->name = $request->name;
        $book->price = $request->price;
        $book->quantity = $request->quantity;
    
        if ($this->user->books()->save($book))
            return response()->json([
                'success' => true,
                'book' => $book
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Sorry, book could not be added'
            ], 500);
    }

    public function update(Request $request, $id)
    {
        $book = $this->user->books()->find($id);
    
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, book with id ' . $id . ' cannot be found'
            ], 400);
        }
    
        $updated = $book->fill($request->all())
            ->save();
    
        if ($updated) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, book could not be updated'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $book = $this->user->books()->find($id);
    
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, book with id ' . $id . ' cannot be found'
            ], 400);
        }
    
        if ($book->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'book could not be deleted'
            ], 500);
        }
    }

}
