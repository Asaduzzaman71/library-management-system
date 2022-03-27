<?php

namespace App\Repositories;

use App\Http\Requests\BookRequest;
use App\Interfaces\BookInterface;
use App\Traits\ResponseAPI;
use App\Models\Book;
use App\Models\BookImage;
use \Illuminate\Support\Facades\DB;
use App\Traits\FileUpload;
use Illuminate\Support\Facades\Storage;


class BookRepository implements BookInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;
    // Use FileUpload Trait in this repository
    use FileUpload;


    public function getAllBooks()
    {
        try {
            $books = Book::get();
            return $this->success("All Books", $books);
        } catch(\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getBookById($id)
    {
        try {
            $book = Book::find($id);
            // Check the Book
            if(!$book) return $this->error("No Books with ID $id", 404);
            return $this->success("Book Details", $book);
        } catch(\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getBooksByCategoryId($id){

        try {
            $books = Book::where('category_id',$id)->latest()->get();
            // Check the Book
            if( count($books) > 0 )   return $this->success("books list", $books);
            return $this->error("No books with category ID $id", 404);

        } catch(\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function requestBook(BookRequest $request, $id = null)
    {
        DB::beginTransaction();
        try {

            $book = $id ? Book::find($id) : new Book;
            // Check the Book
            if($id && !$book) return $this->error("No Book with ID $id", 404);
            $book->category_id = (int)$request->category_id;
            $book->ISBN = $request->ISBN;
            $book->book_name = $request->book_name;
            $book->author_name = $request->author_name;
            $book->edition = $request->edition;
            $book->publisher = $request->publisher;
            $book->rack_no = $request->rack_no;
            $book->no_of_copies = $request->no_of_copies;
            $book->status = $request->status;
            $book->created_by = auth('api')->id();
            $book->updated_by = $id  ? auth('api')->id() : NULL;
            if($request->hasFile('image')){
                $path = $this->FileUpload($request->image,'book');
                $book->image =  $path;
            }
            // Save the Book
            $book->save();
            $book = Book::where('id',$book->id)->first();
            DB::commit();
            return $this->success(
                $id ? "Book updated"
                    : "Book created",
                $book, $id ? 200 : 201);
        } catch(\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deleteBook($id)
    {
        DB::beginTransaction();
        try {
            $book = Book::find($id);
            // Check the Post
            if(!$book) return $this->error("No book with ID $id", 404);

            // Delete the Post
            Storage::disk('public')->delete($book->image);
            $book->delete();
            DB::commit();
            return $this->success("Book deleted", $book);
        } catch(\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

}
