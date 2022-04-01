<?php

namespace App\Repositories;

use App\Http\Requests\MemberRequest;
use App\Interfaces\MemberInterface;
use App\Traits\ResponseAPI;
use App\Models\Member;
use \Illuminate\Support\Facades\DB;
use App\Traits\FileUpload;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class MemberRepository implements MemberInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;
    // Use FileUpload Trait in this repository
    use FileUpload;
    public function getAllMembers()
    {
        try {
            $members = Member::get();
            return $this->success("All Members", $members);
        } catch(\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getMemberById($id)
    {
        try {
            $member = Member::find($id);
            // Check the Book
            if(!$member) return $this->error("No Member with ID $id", 404);
            return $this->success("Member Details", $member);
        } catch(\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function requestMember(MemberRequest $request, $id = null)
    {
        DB::beginTransaction();
        try {
            $member = $id ? Member::find($id) : new Member;
            // Check the Book
            if($id && !$member) return $this->error("No Member with ID $id", 404);
            // Generate unique id for membership
            $membership_uid = IdGenerator::generate(['table' => 'members', 'length' => 10, 'prefix' =>date('ym')]);
            $member->membership_uid =$id  ? $member->membership_uid : $membership_uid  ;
            $member->name = $request->name;
            $member->email = $request->email;
            $member->phone = $request->phone;
            $member->address = $request->address;
            $member->issue_date = $request->issue_date;
            $member->expiary_date = $request->expiary_date;
            $member->created_by = auth()->id();
            $member->updated_by = $id  ? auth()->id() : NULL;
            // Save the Member
            $member->save();
            $member = Member::where('id',$member->id)->first();
            DB::commit();
            return $this->success(
                $id ? "Member updated"
                    : "Member created",
                $member, $id ? 200 : 201);
        } catch(\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function deleteMember($id)
    {
        DB::beginTransaction();
        try {
            $member = Member::find($id);
            // Check the member
            if(!$member) return $this->error("No member with ID $id", 404);
            // Delete the member
            $member->delete();
            DB::commit();
            return $this->success("Member deleted", $member);
        } catch(\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

}
