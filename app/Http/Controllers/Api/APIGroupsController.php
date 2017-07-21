<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\GroupTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Group\EloquentGroupRepository;

class APIGroupsController extends BaseApiController 
{   
    /**
     * Group Transformer
     * 
     * @var Object
     */
    protected $groupTransformer;

    /**
     * Repository
     * 
     * @var Object
     */
    protected $repository;

    /**
     * __construct
     * 
     * @param EventTransformer $groupTransformer
     */
    public function __construct(EloquentGroupRepository $repository, GroupTransformer $groupTransformer)
    {
        parent::__construct();

        $this->repository       = $repository;
        $this->groupTransformer = $groupTransformer;
    }

    /**
     * List of All Groups
     * 
     * @param Request $request
     * @return json
     */
    public function index(Request $request) 
    {
        $userInfo   = $this->getAuthenticatedUser();
        $campusId   = $userInfo->user_meta->campus_id;
        $groups     = $this->repository->getAllGroupsByCampusId($campusId);

        if($groups && count($groups))
        {
            $response = $this->groupTransformer->getAllGroupsWithMembers($groups, $userInfo);
            
            return $this->successResponse($response);
        }

        $error = [
            'reason' => 'Unable to find Groups!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'No Groups Found !');
    }

    /**
     * Create
     * 
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        $input = $request->all();

        if(isset($input['name']) && !empty($input['name']))
        {
            if($request->file('image'))
            {
                $imageName  = rand(11111, 99999) . '_interest.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(base_path() . '/public/groups/', $imageName);
                $input = array_merge($request->all(), ['image' => $imageName]);
            }
            else
            {
                $input = array_merge($request->all(), ['image' => 'default.png']);    
            }

            $userInfo   = $this->getAuthenticatedUser();
            $input      = array_merge($input, ['campus_id' => $userInfo->user_meta->campus_id, 'user_id' => $userInfo->id]);
            $model      = $this->repository->create($input);

            if($model)
            {
                $response = $this->groupTransformer->getSingleGroup($model, $userInfo);
                
                return $this->successResponse($response, 'Group is Created Successfully');
            }
        }

        $error = [
            'reason' => 'Invalid Inputs'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'Something went wrong !');
    }

    /**
     * Edit
     * 
     * @param Request $request
     * @return string
     */
    public function edit(Request $request)
    {
        $groupId = $request->group_id;

        if($groupId)
        {
            $input = $request->all();

            if($request->file('image'))
            {
                $imageName  = rand(11111, 99999) . '_interest.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(base_path() . '/public/groups/', $imageName);
                $input = array_merge($request->all(), ['image' => $imageName]);
            }

            $userInfo   = $this->getAuthenticatedUser();
            $model      = $this->repository->update($groupId, $input);

            if($model)
            {
                $group      = $this->repository->getById($groupId);
                $response   = $this->groupTransformer->getSingleGroup($group, $userInfo);
                
                return $this->successResponse($response, 'Group is Edited Successfully');
            }
        }

        $error = [
            'reason' => 'Invalid Inputs'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'Something went wrong !');
    }

    /**
     * Delete
     * 
     * @param Request $request
     * @return string
     */
    public function delete(Request $request)
    {
        $eventId    = (int) $request->event_id;
        $userInfo   = $this->getAuthenticatedUser();

        if($eventId)
        {
            $status = $this->repository->destroy($eventId, $userInfo);

            if($status)
            {
                $responseData = [
                    'success' => 'Event Deleted'
                ];

                return $this->successResponse($responseData, 'Event is Deleted Successfully');
            }
        }

        $error = [
            'reason' => "You don't have permission to Delete Event!"
        ];

        return $this->setStatusCode(404)->failureResponse($error, 'Something went wrong !');
    }

    /**
     * Join Member
     * 
     * @param Request $request
     * @return json
     */
    public function joinMember(Request $request)
    {
        $groupId    = (int) $request->group_id;
        $userInfo   = $this->getAuthenticatedUser();
        $isLeader   = isset($request->is_leader) ? $request->is_leader : 0;

        if($groupId)
        {
            $status = $this->repository->joinGroup($groupId, $userInfo, $isLeader);

            if($status)
            {
                $responseData = [
                    'success' => 'Group Joined Successfully.'
                ];

                return $this->successResponse($responseData, 'Group Joined Successfully !.');
            }
        }

        $error = [
            'reason' => "Group Not Exists or Unable to Join Group !"
        ];

        return $this->setStatusCode(404)->failureResponse($error, 'Something went wrong !');
    }

    /**
     * Skip Event
     * 
     * @param Request $request
     * @return json
     */
    public function skipEvent(Request $request)
    {
        $eventId    = (int) $request->event_id;
        $userInfo   = $this->getAuthenticatedUser();

        if($eventId)
        {
            $status = $this->repository->removeEventMember($eventId, $userInfo);

            if($status)
            {
                $responseData = [
                    'success' => 'Exit from Event Successfully.'
                ];

                return $this->successResponse($responseData, 'Event removed From your Calendar.');
            }
        }

        $error = [
            'reason' => "Event is not Exists or Deleted!"
        ];

        return $this->setStatusCode(404)->failureResponse($error, 'Something went wrong !');
    }
}
