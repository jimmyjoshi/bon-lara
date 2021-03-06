<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\GroupTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Group\EloquentGroupRepository;
use App\Models\Access\User\User;

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
            $response = $this->groupTransformer->getAllGroupsWithMembers($groups, $userInfo, true);

            return $this->successResponse($response);
        }

        $error = [
            'reason' => 'Unable to find Groups!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'No Groups Found !');
    }

    public function getRandomGroupsByCampusId(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $campusId   = $userInfo->user_meta->campus_id;
        $groups     = $this->repository->getAllFeatureGroupsByCampusId($campusId);
        
        if($groups && count($groups))
        {
            $response = $this->groupTransformer->getAllGroupsWithMembersRandomFive($groups, $userInfo);
            
            return $this->successResponse($response);
        }

        $error = [
            'reason' => 'Unable to find Random Groups!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'No Random Groups Found !');   
    }

    /**
     * Get ForYou Groups
     * 
     * @param Request $request
     * @return array
     */
    public function getForYouGroups(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $groups     = $this->repository->getAllGroupsForYou($userInfo);

        if($groups && count($groups))
        {
            $response = $this->groupTransformer->getAllGroupsWithMembers($groups, $userInfo, true);
            
            return $this->successResponse($response);
        }

        $error = [
            'reason' => 'Unable to find Groups For You!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'No Groups Found For You!');  
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
                    'success' => 'Group Deleted'
                ];

                return $this->successResponse($responseData, 'Group is Deleted Successfully');
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
        $groupId = (int) $request->group_id;

        $isLeader = isset($request->is_leader) ? $request->is_leader : 0;

        if($groupId)
        {
            $userIds = explode(',', $request->get('user_id'));
            $status = $this->repository->joinGroupMultiMembers($groupId, $userIds, $isLeader, $request->get('sync'));
            
            if($status)
            {
                $userInfo   = $this->getAuthenticatedUser();
                $model      = $this->repository->getById($groupId);

                if($model)
                {
                    $response = $this->groupTransformer->getSingleGroup($model, $userInfo);
                    
                    return $this->successResponse($response, 'Group Joined Successfully !');
                }
            }
        }

        $error = [
            'reason' => "Group Not Exists or Unable to Join Group !"
        ];

        return $this->setStatusCode(404)->failureResponse($error, 'Something went wrong !');
    }

    /**
     * Remove Member
     * 
     * @param Request $request
     * @return json
     */
    public function removeMember(Request $request)
    {
        $userInfo = $this->getAuthenticatedUser();
        
        if($request->get('group_id') && $request->get('user_id'))
        {
            $status = $this->repository->removeMember($request->get('group_id'), $request->get('user_id'), $userInfo);    
            
            if($status)
            {
                $responseData = [
                    'success' => 'Group Member Removed Successfully.'
                ];

                return $this->successResponse($responseData, 'Group Member Removed Successfully !.');
            }
        }

        $error = [
            'reason' => "Unable to Remove Group Members, Only Group Leader can Remove Members!"
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

    /**
     * Get Group Members
     * 
     * @param Request $request
     * @return json
     */
    public function getAllGroupMembers(Request $request)
    {
        $userInfo = $this->getAuthenticatedUser();
        
        if($request->get('group_id'))
        {
            $group      = $this->repository->getAllMembersByGroupId($request->get('group_id'));
            $response   = $this->groupTransformer->getGroupMembers($group);
                    
            return $this->successResponse($response, 'Fetch All Memebrs');
        }

        $error = [
            'reason' => "No Members Found !"
        ];

        return $this->setStatusCode(404)->failureResponse($error, 'Something went wrong !');
    }

    /**
     * Exit Group
     * 
     * @param Request $request
     * @return json
     */
    public function exitGroup(Request $request)
    {
        if($request->get('group_id') && $request->get('user_id'))
        {
            $groupId    = (int) $request->group_id;
            $userId     = (int) $request->user_id;
            
            $status = $this->repository->exitGroup($groupId, $userId);

            if($status)
            {
                $responseData = [
                    'success' => 'Exit from Group Successfully.'
                ];

                return $this->successResponse($responseData, 'Exit From Group !');
            }
        }

        $error = [
            'reason' => "Group is not Exists or Deleted!"
        ];

        return $this->setStatusCode(404)->failureResponse($error, 'Something went wrong !');        
    }

    public function getMemberSuggestions(Request $request)
    {
        if($request->get('group_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $group      = $this->repository->getById($request->get('group_id'));
            $allMembers = $this->repository->getAllCampusUsers($userInfo);
            
            if($group && $allMembers)
            {
                $response = $this->groupTransformer->getMemberSuggestions($group, $allMembers);

                return $this->successResponse($response);
            }
        }

        $error = [
            'reason' => "Group is not Exists or Deleted!"
        ];

        return $this->setStatusCode(404)->failureResponse($error, 'Something went wrong !');   
    }

    /**
     * Allow Member Permissions
     * 
     * @param Request $request
     * @return json
     */
    public function allowMemberPermissions(Request $request)
    {
        if($request->get('user_id') && $request->get('group_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $status = $this->repository->allowMemberPermissions($userInfo, $request->get('group_id'), $request->get('user_id'));
            
            if($status)
            {
                $group      = $this->repository->getById($request->get('group_id'));                  
                $response   = $this->groupTransformer->getSingleGroup($group, $userInfo);

                return $this->successResponse($response, 'Group is Created Successfully');
            }
        }

        $error = [
            'reason' => "Unable to Allow Group Permissions"
        ];

        return $this->setStatusCode(404)->failureResponse($error, 'Something went wrong !'); 
    }

    /**
     * Change MemberStatus
     * 
     * @param  Request $request [description]
     * @return json
     */
    public function changeMemberStatus(Request $request)
    {
        if($request->get('user_id') && $request->get('group_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $status = $this->repository->changeMemberStatus($userInfo, $request->get('group_id'), $request->get('user_id'), $request->get('member_status'));
            
            if($status)
            {
                $group      = $this->repository->getById($request->get('group_id'));                  
                $response   = $this->groupTransformer->getSingleGroup($group, $userInfo);

                return $this->successResponse($response, 'Member Status Updated Successfully !');
            }
        }

        $error = [
            'reason' => "Unable to Allow Member Permissions"
        ];

        return $this->setStatusCode(404)->failureResponse($error, 'Something went wrong !');    
    }

    public function allowPrivateGroupAccess(Request $request)
    {
        if($request->get('user_id') && $request->get('group_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $feedId     = $request->get('feed_id') ? $request->get('feed_id') : 0;

            $status = $this->repository->allowPrivateGroupAccess($userInfo, $request->get('group_id'), $request->get('user_id'), $feedId);

            if($status)
            {
                $group      = $this->repository->getById($request->get('group_id'));                  
                $response   = $this->groupTransformer->getSingleGroup($group, $userInfo);

                return $this->successResponse($response, 'Member Permission Updated Successfully !');
            }
        }

        $error = [
            'reason' => "Unable to Allow Member Permissions"
        ];

        return $this->setStatusCode(404)->failureResponse($error, 'Something went wrong !');  
    }

    public function removePrivateGroupAccess(Request $request)
    {
        if($request->get('user_id') && $request->get('group_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $feedId     = $request->get('feed_id') ? $request->get('feed_id') : 0;

            $status = $this->repository->removePrivateGroupAccess($userInfo, $request->get('group_id'), $request->get('user_id'), $feedId);

            if($status)
            {
                $group      = $this->repository->getById($request->get('group_id'));                  
                $response   = $this->groupTransformer->getSingleGroup($group, $userInfo);

                return $this->successResponse($response, 'Member Permission Updated Successfully !');
            }
        }

        $error = [
            'reason' => "Unable to Allow Member Permissions"
        ];

        return $this->setStatusCode(404)->failureResponse($error, 'Something went wrong !');
    }
}

