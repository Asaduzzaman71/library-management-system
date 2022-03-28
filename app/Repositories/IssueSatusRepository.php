<?php

namespace App\Repositories;

use App\Http\Requests\IssueStatusRequest;
use App\Interfaces\IssueStatusInterface;
use App\Traits\ResponseAPI;
use App\Models\IssueStatus;
use \Illuminate\Support\Facades\DB;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class IssueStatusRepository implements IssueStatusInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;


    public function getAllIssueStatus()
    {
        try {
            $issueStatuses = IssueStatus::get();
            return $this->success("All Issue Status", $issueStatuses);
        } catch(\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getIssueStatusById($id)
    {
        try {
            $issueStatus = IssueStatus::find($id);
            // Check the Book Issue Status
            if(!$issueStatus) return $this->error("No Issue Status with ID $id", 404);
            return $this->success("Issue Status Details", $issueStatus);
        } catch(\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }



    public function requestIssueStatus(IssueStatusRequest $request, $id = null)
    {
        DB::beginTransaction();
        try {

            $issueStatus = $id ? IssueStatus::find($id) : new IssueStatus;
            // Check the Book
            if($id && !$issueStatus) return $this->error("No Issue Status with ID $id", 404);
            $issueStatus->member_id = $request->member_id;
            $issueStatus->book_id = $request->book_id;
            $issueStatus->issue_date = $request->issue_date;
            $issueStatus->return_date = $request->return_date;
            $issueStatus->created_by = auth('api')->id();
            $issueStatus->updated_by = $id  ? auth('api')->id() : NULL;

            // Save the Member
            $issueStatus->save();
            $issueStatus = IssueStatus::where('id',$issueStatus->id)->first();
            DB::commit();
            return $this->success(
                $id ? "Issue Status updated"
                    : "Issue Status created",
                $issueStatus, $id ? 200 : 201);
        } catch(\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }

    }

    public function deleteIssueStatus($id)
    {
        DB::beginTransaction();
        try {
            $issueStatus = IssueStatus::find($id);
            // Check the member
            if(!$issueStatus) return $this->error("No Issue Status with ID $id", 404);

            // Delete the member
            $issueStatus->delete();
            DB::commit();
            return $this->success("Issue Status deleted", $issueStatus);
        } catch(\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

}
