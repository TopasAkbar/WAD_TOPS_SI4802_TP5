<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        return response()->json([
            'status' => true,
            'message' => 'Books retrieved successfully',
            'data' => BookResource::collection($books)
        ], 200);
    }

    /**
     * ==========2===========
     * Simpan buku baru ke dalam penyimpanan.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'title'           => 'required|string',
            'author'          => 'required|string',
            'published_year'  => 'required|integer|digits:4',
            'is_available'    => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Simpan data buku
        $book = Book::create($validator->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Book created successfully',
            'data'    => new BookResource($book)
        ], 201);
    }

    /**
     * =========3===========
     * Tampilkan detail buku tertentu.
     */
    public function show(string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'status'  => false,
                'message' => 'Book not found'
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Book retrieved successfully',
            'data'    => new BookResource($book)
        ], 200);
    }

    /**
     * =========4===========
     * Fungsi untuk memperbarui data buku tertentu
     */
    public function update(Request $request, string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'status'  => false,
                'message' => 'Book not found'
            ], 404);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'title'           => 'required|string',
            'author'          => 'required|string',
            'published_year'  => 'required|integer|digits:4',
            'is_available'    => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update buku
        $book->update($validator->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Book updated successfully',
            'data'    => new BookResource($book)
        ], 200);
    }

    /**
     * =========5===========
     * Hapus buku tertentu dari penyimpanan.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'status'  => false,
                'message' => 'Book not found'
            ], 404);
        }

        $book->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Book deleted successfully'
        ], 200);
    }

    /**
     * =========6===========
     * Ubah status ketersediaan buku (ubah field is_available)
     */
    public function borrowReturn(string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'status'  => false,
                'message' => 'Book not found'
            ], 404);
        }

        // Toggle availability
        $book->is_available = !$book->is_available;
        $book->save();

        return response()->json([
            'status'  => true,
            'message' => 'Book availability updated',
            'data'    => new BookResource($book)
        ], 200);
    }
}
