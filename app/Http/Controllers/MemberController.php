<?php

namespace App\Http\Controllers;
use App\Interfaces\MemberInterface;
use App\Http\Requests\MemberRequest;

class MemberController extends Controller
{
    protected $memberInterface;
    public function __construct(MemberInterface $memberInterface)
    {
        $this->memberInterface = $memberInterface;
    }
    public function index(){
        return $this->memberInterface->getAllMembers();
    }
    public function store(MemberRequest $request){
        return $this->memberInterface->requestMember($request);
    }
    public function update(MemberRequest $request, $id){
        return $this->memberInterface->requestMember($request, $id);
    }

    public function destroy($id)
    {
        return $this->memberInterface->deleteMember($id);
    }
}
