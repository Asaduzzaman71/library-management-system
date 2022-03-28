<?php

namespace App\Repositories;

use App\Http\Requests\IssueStatusRequest;
use App\Interfaces\IssueStatusInterface;
use App\Traits\ResponseAPI;
use App\Models\IssueStatus;
use \Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

            $noOfBookIssuedToday = IssueStatus::where('member_id',1)->whereDate('created_at', Carbon::today()->format('Y-m-d'))->count();
            $noOfBooksIssuedByAuthUser = IssueStatus::where('member_id',1)->count();
            $noOfBooksReturnedByAuthUser = IssueStatus::where('member_id',1)->whereNotNull('return_date')->count();
            $noOfBooksIssuedCurrentMonth = IssueStatus::whereMonth('issue_date', Carbon::now()->month)->count();
            $noOfBooksLeftToReturn = $noOfBooksReturnedByAuthUser -  $noOfBooksIssuedByAuthUser;

            if( $noOfBookIssuedToday == 2 ){
                return $this->error("You have already taken 2 books today", 403);
            }
            elseif( $noOfBooksLeftToReturn == 3 ){
                return $this->error("You have already taken 2 books today", 403);
            }
            elseif( $noOfBooksIssuedCurrentMonth == 12 ){
                return $this->error("You have already taken 12 books this month", 403);
            }
            else{

                $issueStatus = $id ? IssueStatus::find($id) : new IssueStatus;
                if($id && !$issueStatus) return $this->error("No Issue Status with ID $id", 404);
                $issueStatus->member_id = $request->member_id;
                $issueStatus->book_id = $request->book_id;
                $issueStatus->issue_date = $request->issue_date;
                $issueStatus->due_date = $request->due_date;
                $issueStatus->created_by = auth('api')->id();
                $issueStatus->updated_by = $id  ? auth('api')->id() : NULL;
                $issueStatus->save();
                $issueStatus = IssueStatus::where('id',$issueStatus->id)->first();
                //DB::commit();
                return $this->success(
                    $id ? "Issue Status updated"
                        : "Issue Status created",
                    $issueStatus, $id ? 200 : 201);

            }

        } catch(\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }

    }


    public function updateReturnStatus($id){

        $issueStatus = IssueStatus::find($id);
        if(!$issueStatus) return $this->error("No Issue Status with ID $id", 404);
        $issueStatus->return_date = Carbon::today()->format('Y-m-d');
        $issueStatus->save();
        $issueStatus = IssueStatus::where('id',$issueStatus->id)->first();
        return $this->success("Return Date Updated", $issueStatus);

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
