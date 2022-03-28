<?php

namespace App\Interfaces;

use App\Http\Requests\MemberRequest;

interface MemberInterface
{

    public function getAllMembers();

    public function getMemberById($id);

    public function requestMember(MemberRequest $request, $id = null);

    public function deleteMember($id);
}
