<?php

namespace App\Interfaces;

use App\Http\Requests\IssueStatusRequest;

interface IssueStatusInterface
{

    public function getAllIssueStatus();

    public function getIssueStatusById($id);

    public function requestIssueStatus(IssueStatusRequest $request, $id = null);

    public function deleteIssueStatus($id);
}
