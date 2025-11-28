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
        return response()->json($books);
    }

    /**
     * ==========2===========
     * Simpan buku baru ke dalam penyimpanan.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'published_year' => 'required|integer|digits:4',
            'is_available' => 'boolean',
        ]);
        if ($validator->fails()){
            return response()->json([
                'message' => 'Coba check lagi ganteng',
                'errors' => $validator->errors()
            ], 422);
        }
        $book = Book::create($validator->validated());
        return (new BookResource($book))
                    ->additional(['message' => 'Book nya sukses yaa'])
                    ->response()
                    ->setStatusCode(201);
    }

    /**
     * =========3===========
     * Tampilkan detail buku tertentu.
     */
    public function show(string $id)
    {
        $book = Book::find($id);
        if(!$book){
            return response()->json(['message' => 'Books tidak di temukan.'], 404);
        }
        return new BookResource($book);
    }

    /**
     * =========4===========
     * Fungsi untuk memperbarui data buku tertentu
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'sometimes|required|string|max:255',
            'author' => 'sometimes|required|string|max:255',
            'published_year' => 'sometimes|required|digits:4',
            'is_available' => 'sometimes|boolean',
        ]);
        $book = Book::find($id);
        if(!$book){
            return response()->json(['message' => 'Book tidak di temukan gimana nih'], 404);
        }
        if($validator->fails()){
            return response()->json([
                'message' => 'tolong check lagi ya brody',
                'errors' => $validator->errors()
            ], 422);
        }
        $book->update($validator->validated());

        return (new BookResource($book))
                    ->additional(['message' => 'Book berhasil di update'])
                    ->response()
                    ->setStatusCode(200);
    }

    /**
     * =========5===========
     * Hapus buku tertentu dari penyimpanan.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);
        if(!$book){
            return response()->json(['message' => 'book tidak ditemukan'], 404);

        }
        $book->delete();
        return response()->json(['message' => 'book berhasil di delete'], 200);
    }

    /**
     * =========6===========
     * Ubah status ketersediaan buku (ubah field is_available)
     */
    public function borrowReturn(string $id)
    {
        $book = Book::find($id);
        if (!$book) {
            return response()->json(['message' => 'Book tidak ditemukan'], 404);
        }
        $book->is_available = !$book->is_available;
        $book->save();
        return (new BookResource($book))
            ->additional(['message' => 'Status buku berhasil diubah guysss'])
            ->response()
            ->setStatusCode(200);
    }
}