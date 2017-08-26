<?php namespace App\Repositories\Group;

use App\Models\Group\Group;
use App\Models\Group\GroupMember;
use App\Models\Campus\Campus;
use App\Models\Group\GroupInterest;
use App\Models\Access\User\User;
use App\Repositories\DbRepository;
use App\Exceptions\GeneralException;

class EloquentGroupRepository extends DbRepository
{
	/**
	 * Event Model
	 * 
	 * @var Object
	 */
	public $model;

	/**
	 * Module Title
	 * 
	 * @var string
	 */
	public $moduleTitle = 'Group';

	/**
	 * Table Headers
	 *
	 * @var array
	 */
	public $tableHeaders = [
		'name' 			=> 'Group Name',
		'description' 	=> 'Details',
		'group_leader'	=> 'Group Leader',
		'members_count'	=> 'Members Count',
		'campus_name' 	=> 'Campus',
		'is_private' 	=> 'Private Group',
		'group_type' 	=> 'Discovery Type',
		'actions' 		=> 'Actions'
	];

	/**
	 * Table Columns
	 *
	 * @var array
	 */
	public $tableColumns = [
		'name' =>	[
			'data' 			=> 'name',
			'name' 			=> 'name',
			'searchable' 	=> true, 
			'sortable'		=> true
		],
		'description' => [
			'data' 			=> 'description',
			'name' 			=> 'description',
			'searchable' 	=> true, 
			'sortable'		=> true
		],
		'group_leader'	=> [
			'data' 			=> 'group_leader',
			'name' 			=> 'group_leader',
			'searchable' 	=> true, 
			'sortable'		=> true
		],
		'members_count'	=> [
			'data' 			=> 'members_count',
			'name' 			=> 'members_count',
			'searchable' 	=> false, 
			'sortable'		=> false
		],
		'campus_name' => [
			'data' 			=> 'campus_name',
			'name' 			=> 'campus_name',
			'searchable' 	=> true, 
			'sortable'		=> true
		],
		'is_private' => [
			'data' 			=> 'is_private',
			'name' 			=> 'is_private',
			'searchable' 	=> true, 
			'sortable'		=> true
		],
		'group_type' => [
			'data' 			=> 'group_type',
			'name' 			=> 'group_type',
			'searchable' 	=> false, 
			'sortable'		=> false
		],
		'actions' => [
			'data' 			=> 'actions',
			'name' 			=> 'actions',
			'searchable' 	=> false, 
			'sortable'		=> false
		]
	];

	/**
	 * Is Admin
	 * 
	 * @var boolean
	 */
	protected $isAdmin = false;

	/**
	 * Admin Route Prefix
	 * 
	 * @var string
	 */
	public $adminRoutePrefix = 'admin';

	/**
	 * Client Route Prefix
	 * 
	 * @var string
	 */
	public $clientRoutePrefix = 'frontend';

	/**
	 * Admin View Prefix
	 * 
	 * @var string
	 */
	public $adminViewPrefix = 'backend';

	/**
	 * Client View Prefix
	 * 
	 * @var string
	 */
	public $clientViewPrefix = 'frontend';

	/**
	 * Module Routes
	 * 
	 * @var array
	 */
	public $moduleRoutes = [
		'listRoute' 	=> 'group.index',
		'createRoute' 	=> 'group.create',
		'viewRoute' 	=> 'group.show',
		'storeRoute' 	=> 'group.store',
		'editRoute' 	=> 'group.edit',
		'updateRoute' 	=> 'group.update',
		'deleteRoute' 	=> 'group.destroy',
		'dataRoute' 	=> 'group.get-list-data'
	];

	/**
	 * Module Views
	 * 
	 * @var array
	 */
	public $moduleViews = [
		'listView' 		=> 'group.index',
		'showView' 		=> 'group.show',
		'createView' 	=> 'group.create',
		'editView' 		=> 'group.edit',
		'deleteView' 	=> 'group.destroy',
	];

	/**
	 * Construct
	 *
	 */
	public function __construct()
	{
		$this->model 			= new Group;
		$this->userModel 		= new User;
		$this->groupMember  	= new GroupMember;
		$this->campusModel 		= new Campus;
	}

	/**
	 * Create Group
	 *
	 * @param array $input
	 * @return mixed
	 */
	public function create($input)
	{
		$input = $this->prepareInputData($input, true);
		$model = $this->model->create($input);

		if($model)
		{
			if(isset($input['interests']))
			{
				$interestStatus = $this->addGroupInterest($model, $input);
			}

			$groupMemberData = [
		    		'group_id'	=> $model->id,
		    		'user_id'	=> access()->user()->id,
		    		'is_leader'	=> 1
		    	];

		   	$this->groupMember->create($groupMemberData);
			
			return $model;
		}

		return false;
	}	

	/**
	 * Add Group Interest
	 *
	 * @param object $model
	 * @param array $input
	 */
	public function addGroupInterest($model = null, $input = array())
	{
		$groupInterest = [];

		if(is_array($input['interests']))
		{
			$interests = $input['interests'];
		}
		else
		{
			$interests = explode(',', $input['interests']);
		}

		foreach($interests as $interest)
		{
			$groupInterest[] = [
				'group_id' 		=> $model->id,
				'interest_id' 	=> $interest
			];
		}

		if(count($groupInterest))
		{
			GroupInterest::where(['group_id' => $model->id])->delete();
			
			return GroupInterest::insert($groupInterest);
		}

		return true;
	}

	
	/**
	 * Update Group
	 *
	 * @param int $id
	 * @param array $input
	 * @return bool|int|mixed
	 */
	public function update($id, $input)
	{
		$model = $this->model->find($id);

		if($model)
		{
			$input = $this->prepareInputData($input);		

			if(isset($input['interests']))
			{
				$interestStatus = $this->addGroupInterest($model, $input);
			}
			
			return $model->update($input);
		}

		return false;
	}

	/**
	 * Destroy Group
	 *
	 * @param object $user
	 * @param int $id
	 * @return mixed
	 * @throws GeneralException
	 */
	public function destroy($id, $user = null)
	{
		$model = $this->model->find($id);

		if(!$model)
		{
			return false;
		}

		if($user)
		{
			if($user->id != $model->user_id )
			{
				return false;
			}
		}
			
		if($model)
		{
			return $model->delete();
		}

		return  false;
	}

	/**
     * Get All
     *
     * @param string $orderBy
     * @param string $sort
     * @return mixed
     */
    public function getAll($orderBy = 'id', $sort = 'asc')
    {
    	return $this->model->get();
    }

	/**
     * Get by Id
     *
     * @param int $id
     * @return mixed
     */
    public function getById($id = null)
    {
    	if($id)
    	{
    		return $this->model->find($id);
    	}
        
        return false;
    }   

    /**
     * Get Table Fields
     * 
     * @return array
     */
    public function getTableFields()
    {
    	return [
			$this->model->getTable().'.id as id',
			$this->model->getTable().'.name',
			$this->model->getTable().'.description',
			$this->model->getTable().'.is_private',
			$this->model->getTable().'.group_type',
			$this->userModel->getTable().'.name as username',
			$this->campusModel->getTable().'.name as campus_name',
		];
    }

    /**
     * @return mixed
     */
    public function getForDataTable()
    {
    	return  $this->model->select($this->getTableFields())
    			->leftjoin($this->userModel->getTable(), $this->userModel->getTable().'.id', '=', $this->model->getTable().'.user_id')
    			->leftjoin($this->campusModel->getTable(), $this->campusModel->getTable().'.id', '=', $this->model->getTable().'.campus_id')
    			->get();
        
    }

    /**
     * Set Admin
     *
     * @param boolean $isAdmin [description]
     */
    public function setAdmin($isAdmin = false)
    {
    	$this->isAdmin = $isAdmin;

        return $this;
    }

    /**
     * Prepare Input Data
     * 
     * @param array $input
     * @param bool $isCreate
     * @return array
     */
    public function prepareInputData($input = array(), $isCreate = false)
    {
    	if($isCreate)
    	{
    		$input = array_merge($input, ['user_id' => access()->user()->id]);
    	}

    	return $input;
    }

    /**
     * Get Table Headers
     * 
     * @return string
     */
    public function getTableHeaders()
    {
    	if($this->isAdmin)
    	{
    		return json_encode($this->setTableStructure($this->tableHeaders));
    	}

    	$clientHeaders = $this->tableHeaders;

    	return json_encode($this->setTableStructure($clientHeaders));
    }

	/**
     * Get Table Columns
     *
     * @return string
     */
    public function getTableColumns()
    {
    	if($this->isAdmin)
    	{
    		return json_encode($this->setTableStructure($this->tableColumns));
    	}

    	$clientColumns = $this->tableColumns;

    	return json_encode($this->setTableStructure($clientColumns));
    }

    /**
     * Join Group
     * 
     * @param int $groupId
     * @param object $user
     * @param int $isLeader
     * @return bool
     */
    public function joinGroup($groupId = null, $user = null, $isLeader = 0)
    {
    	if($groupId && $user)
    	{
    		$status = $this->groupMember->where(['group_id' => $groupId, 'user_id' => $user->id])->first();

    		if($status)
	    	{
	    		return false;
	    	}
    		
    		if($isLeader == 1)
    		{
    			$groupMemberData = [
		    		'group_id'	=> $groupId,
		    		'user_id'	=> $user->id,
		    		'is_leader'	=> $isLeader
		    	];
    		}
    		else
    		{
    			$groupInfo = $this->model->find($groupId);

    			$groupMemberData = [
		    		'group_id'	=> $groupId,
		    		'user_id'	=> $user->id,
		    		'is_leader'	=> $isLeader,
		    		'status' 	=> $groupInfo->is_private ? 0 : 1
		    	];
    		}

	    	return $this->groupMember->create($groupMemberData);
    	}

    	return false;
    }

    /**
     * Join Group Multi Members
     * 
     * @param int $groupId
     * @param array $userIds
     * @param integer $isLeader
     * @return bool
     */
    public function joinGroupMultiMembers($groupId = null, $userIds = array(), $isLeader = 0, $sync = 0)
    {
    	$groupMemberData = [];

    	if($groupId && count($userIds))
    	{
    		if($sync && $sync == 1)
	    	{
	    		$this->groupMember->where(['group_id' => $groupId, 'is_leader' => $isLeader])->delete();
	    	}

    		$groupInfo = $this->model->find($groupId);

    		foreach($userIds as $userId)	
    		{
    			if(! $userId)
    			{
    				continue;
    			}
		    	
		    	if($isLeader == 1)
	    		{
	    			$groupMemberData[] = [
			    		'group_id'	=> $groupId,
			    		'user_id'	=> $userId,
			    		'is_leader'	=> $isLeader
			    	];
	    		}
	    		else
	    		{
	    			$groupMemberData[] = [
			    		'group_id'	=> $groupId,
			    		'user_id'	=> $userId,
			    		'is_leader'	=> $isLeader,
			    		'status' 	=> $groupInfo->is_private ? 0 : 1
			    	];
	    		}
	    	}
		    
		    if(count($groupMemberData))
		    {
		    	return $this->groupMember->insert($groupMemberData);
		    }
    	}

    	return false;
    }

    /**
     * Remove Event Member
     * @param int $eventId
     * @param object $user
     * @return bool
     */
    public function removeEventMember($eventId = null, $user = null)
	{
		if($eventId && $user)
    	{
			$eventMember = EventMember::where(['event_id' => $eventId, 'user_id' => $user->id])->first();

			if(! $eventMember)
			{
				return false;
			}
			
			return $eventMember->delete();
		}

		return false;
	}

	/**
	 * Get All Groups By CampusId
	 * 
	 * @param int $campusId
	 * @return object
	 */
	public function getAllGroupsByCampusId($campusId = null)
	{
		if($campusId)
		{
			return $this->model->with(['campus', 'user', 'group_members'])->where(['campus_id' => $campusId])->orderBy('name')->get();
		}

		return false;
	}

	/**
	 * Get All Groups For You
	 * 
	 * @param object $user
	 * @return object
	 */
	public function getAllGroupsForYou($user = null)
	{
		if($user)
		{
			$userInterest = $user->user_interests->pluck('interest_id')->toArray();

			if(count($userInterest))
			{
				$responseGroup 	= [];
				
				$groups = $this->model->with(['campus', 'user', 'group_members'])->where('campus_id', $user->user_meta->campus_id)->get();

				foreach($groups as $group)
				{	
					foreach($group->get_group_interests as $interest)
					{
						if(in_array($interest->id, $userInterest))
						{
							$responseGroup[] = $group;
						}
					}
				}

				return $responseGroup;
			}
		}

		return false;
	}

	/**
	 * Get Group Members
	 * 
	 * @param int $groupId
	 * @return array|bool|mixed
	 */
	public function getGroupMembers($groupId = null)
	{
		if($groupId)	
		{
			$model = $this->model->find($groupId);

			return $model->get_only_group_members();
		}

		return false;
	}

	/**
	 * Get All Members By GroupId
	 * 
	 * @param int $groupId
	 * @return array
	 */
	public function getAllMembersByGroupId($groupId = null)
	{
		if($groupId)	
		{
			return $this->model->with(['group_members', 'get_group_leaders'])->where('id', $groupId)->first();
		}

		return false;
	}

	/**
	 * Remove Member
	 * 
	 * @param int $groupId
	 * @param mix $userIds
	 * @param object $userInfo
	 * @return bool
	 */
	public function removeMember($groupId = null, $userIds, $userInfo = null)
	{
		if($groupId && $userInfo)
		{
			$flag  = false;
			$model = $this->model->find($groupId);

			if($model->getLeaders())
			{
				foreach($model->getLeaders() as $leaders)
				{
					if($leaders->id == $userInfo->id)
					{
						$flag = true;
					}
				}

				if($flag)
				{
					if(is_array($userIds))
					{
						foreach($userIds as $userId)
						{
							$this->groupMember->where(['group_id' => $groupId, 'user_id' => $userId])->delete();
						}

						return true;
					}
					else
					{
						return $this->groupMember->where(['group_id' => $groupId, 'user_id' => $userIds])->delete();
					}
				}
			}
		}

		return false;
	}

	/**
	 * Exit Group
	 * 
	 * @param int $groupId
	 * @param int $userId
	 * @return bool
	 */
	public function exitGroup($groupId = null, $userId = null)
	{
		if($groupId)
		{
			$model = $this->model->find($groupId);

			if($model)
			{
				$groupMember = $this->groupMember->where(['group_id' => $groupId, 'user_id' => $userId])->first();
				
				if($groupMember)
				{
					if($groupMember->is_leader && $groupMember->is_leader == 1)	
					{
						$this->groupMember->where(['group_id' => $groupId, 'user_id' => $userId])->delete();

						$nextGroupMember = $this->groupMember->where(['group_id' => $groupId])->orderBy('id', 'desc')->first();

						if($nextGroupMember)
						{
							$nextGroupMember->is_leader = 1;
							return $nextGroupMember->save();
						}
						else
						{
							return $this->model->where('id', $groupId)->delete();
						}
					}

					return $this->groupMember->where(['group_id' => $groupId, 'user_id' => $userId])->delete();
				}

			}

		}

		return false;
	}

	public function getAllCampusUsers($user = null)
    {
        $campusId = $user->user_meta->campus_id;

        return $this->userModel->get()->filter(function($item)  use ($campusId)
        {
            if($item->user_meta && $campusId == $item->user_meta->campus_id)
            {
                return $item;
            }
        });
    }

    /**
     * Allow Member Permissions
     * 
     * @param object $userInfo
     * @param int $groupId
     * @param mixed $userIds
     * @return bool
     */
    public function allowMemberPermissions($userInfo, $groupId = null, $userIds)
    {
    	if($userInfo->id && $groupId)
    	{
    		$groupMemberModel = new GroupMember;

    		$model = $this->model->find($groupId);

    		if($model->is_private == 0)
    		{
    			return true;
    		}
    		
    		$flag  = false;

    		if($model && $model->getLeaders())
			{
				foreach($model->getLeaders() as $leaders)
				{
					if($leaders->id == $userInfo->id)
					{
						$flag = true;
					}
				}

	    		if($flag)
	    		{
	    			$groupMemberInfo 	= [];
	    			$gropuMemberIds 	= $model->get_only_group_members()->pluck('id')->toArray();
	    			$updateInfo  =[
	    				'status' => 0
	    			];

	    			$groupMemberModel->whereIn('user_id', $gropuMemberIds)->update(['status' => 0]);
	    			
	    			if(strpos($userIds, ',') !== false )
					 {
					 	$userIds = explode(',', $userIds);
					 }

	    			if(is_array($userIds))
	    			{
	    				foreach($userIds as $userId)
	    				{
	    					if($userId && in_array($userId, $gropuMemberIds))
	    					{
	    						$this->groupMember->where('user_id', $userId)->update(['status' => 1]);
	    						continue;
	    					}

	    					if($userId)
	    					{
	    						$groupMemberInfo[] = [
		    						'group_id' 	=> $groupId,
		    						'user_id' 	=> $userId,
		    						'is_leader'	=> 0,
		    						'status'	=> 1
		    					];
	    					}
	    				}

	    				return $this->groupMember->insert($groupMemberInfo);
	    			}
	    		}
			}
    	}

    	return false;
    }

    public function getRandomGroupsByCampusId($campusId = null)
    {
    	if($campusId)
    	{
    		$allGroups = $this->model->with(['campus', 'user', 'group_members'])->where(['campus_id' => $campusId])->get()->toArray();

			$fiveGroups = array_rand($allGroups, 5);

			$return = []; 

			foreach($allGroups as $group)
			{
				if(in_array($group['id'], $fiveGroups))
				{
					$return[] = $group;
				}
			}

    		return collect($return);
    	}

    	return false;
    }
}