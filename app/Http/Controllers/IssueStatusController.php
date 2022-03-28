<?php

namespace App\Http\Controllers;
use App\Interfaces\IssueStatusInterface;
use App\Http\Requests\IssueStatusRequest;

class IssueStatusController extends Controller
{
    protected $issueStatusInterface;
    public function __construct(IssueStatusInterface $issueStatusInterface)
    {
        $this->issueStatusInterface = $issueStatusInterface;
    }
    public function index(){
        return $this->issueStatusInterface->getAllIssueStatus();
    }
    public function store(IssueStatusRequest $request){
        return $this->issueStatusInterface->requestIssueStatus($request);
    }
    public function update(IssueStatusRequest $request, $id){
        return $this->issueStatusInterface->requestIssueStatus($request, $id);
    }
    public function setReturnDate($id){
        return $this->issueStatusInterface->updateReturnStatus($id);
    }

    public function destroy($id)
    {
        return $this->issueStatusInterface->deleteIssueStatus($id);
    }
}
