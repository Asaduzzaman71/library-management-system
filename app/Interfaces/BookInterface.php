<?php

namespace App\Interfaces;

use App\Http\Requests\BookRequest;

interface BookInterface
{

    public function getAllBooks();

    public function getBookById($id);

    public function requestBook(BookRequest $request, $id = null);

    public function deleteBook($id);
}
