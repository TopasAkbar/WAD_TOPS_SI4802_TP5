<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Book;
use App\Http\Resources\BookResource;

class BooksController extends Controller
{
    /**
     * ==========1===========
     * Tampilkan daftar semua buku
     */
    public function index()
    {
        $books = Book::all();
        return BookResource::collection($books);
    }

    /**
     * ==========2===========
     * Simpan buku baru ke dalam penyimpanan.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:225',
            'author' => 'required|string|max:50',
            'published_year' => 'required|integer',
            'is_available' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'please check your request',
                'errors' => $validator->errors()
            ], 422);
        }
        $books = Book::create($validator->validated());
        return (new BookResource($books))
        ->additional(['message' => 'books created successfully'])
        ->response()
        ->setStatusCode(201);
    }

    /**
     * =========3===========
     * Tampilkan detail buku tertentu.
     */
    public function show(string $id)
    {
        $books = Book::find($id);
        if (!$books) {
            return response()->json(['message' => 'book not found'], 404);
        }
        return new BookResource($books);
    }

    /**
     * =========4===========
     * Fungsi untuk memperbarui data buku tertentu
     */
    public function update(Request $request, string $id)
    {
         $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:225',
            'author' => 'required|string|max:50',
            'published_year' => 'required|integer',
            'is_available' => 'boolean',
         ]);

         $books = Book::find($id);
         if (!$books) {
            return response()->json(['message' => 'books not found'], 404);
         }
         if ($validator->fails()) {
            return response()->json([
                'message' => 'please check your request',
                'errors' => $validator->errors()
            ], 422);
        }
        $books->update($validator->validated());

        return (new BookResource($books))
        ->additional(['message' => 'books update successfully'])
        ->response()
        ->setStatusCode(200);
    }

    /**
     * =========5===========
     * Hapus buku tertentu dari penyimpanan.
     */
    public function destroy(string $id)
    {
        $books = Book::find($id);

        if(!$books){
            return response()->json(['message' => 'books not found'], 404);
        }
        $books->delete();
        return response()->json(['message' => 'books delete successfully'], 200);
    }

    /**
     * =========6===========
     * Ubah status ketersediaan buku (ubah field is_available)
     */
    public function borrowReturn(string $id)
    {
        $book = Book::findOrFail($id);

        $book->is_available = !$book->is_available;
        $book->save();
        return response()->json(['message' => 'books status successfully update'], 200);

    }
}