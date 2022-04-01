<?php

namespace App\Http\Controllers;
use App\Interfaces\BookInterface;
use App\Http\Requests\BookRequest;
class BookController extends Controller
{
    protected $bookInterface;
    public function __construct(BookInterface $bookInterface)
    {
        $this->bookInterface = $bookInterface;
    }
    public function index(){
        return $this->bookInterface->getAllBooks();
    }
    public function store(BookRequest $request){
        return $this->bookInterface->requestBook($request);
    }
    public function update(BookRequest $request, $book){
        return $this->bookInterface->requestBook($request, $book);
    }
    public function destroy($id)
    {
        return $this->bookInterface->deleteBook($id);
    }
}
