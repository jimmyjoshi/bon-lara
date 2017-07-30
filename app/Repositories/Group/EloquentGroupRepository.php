<?php namespace App\Repositories\Group;

use App\Models\Group\Group;
use App\Models\Group\GroupMember;
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
		'is_private' 	=> 'Access',
		'group_type' 	=> 'Group Type',
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
		$this->model 		= new Group;
		$this->userModel 	= new User;
		$this->groupMember  = new GroupMember;
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
			
			return $model;
		}

		return false;
	}	

	/**
	 * Add Group Interest
	 *
	 * @param object $model
	 * @param array $input [description]
	 */
	public function addGroupInterest($model = null, $input = array())
	{
		$groupInterest = [];

		foreach($input['interests'] as $interest)
		{
			$groupInterest[] = [
				'group_id' 		=> $model->id,
				'interest_id' 	=> $interest
			];
		}

		if(count($groupInterest))
		{
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
			$this->userModel->getTable().'.name as username'
		];
    }

    /**
     * @return mixed
     */
    public function getForDataTable()
    {
    	return  $this->model->select($this->getTableFields())
    			->leftjoin($this->userModel->getTable(), $this->userModel->getTable().'.id', '=', $this->model->getTable().'.user_id')->get();
        
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
	    	
	    	$groupMemberData = [
	    		'group_id'	=> $groupId,
	    		'user_id'	=> $user->id,
	    		'is_leader'	=> $isLeader
	    	];

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
    public function joinGroupMultiMembers($groupId = null, $userIds = array(), $isLeader = 0)
    {
    	$groupMemberData = [];

    	if($groupId && count($userIds))
    	{
    		foreach($userIds as $userId)	
    		{
    			$status = $this->groupMember->where(['group_id' => $groupId, 'user_id' => $userId])->first();

		    	if($status)
		    	{
		    		continue;
		    	}
		    	
		    	$groupMemberData[] = [
		    		'group_id'	=> $groupId,
		    		'user_id'	=> $userId,
		    		'is_leader'	=> $isLeader
		    	];

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
			return $this->model->with(['campus', 'user', 'group_members'])->where(['campus_id' => $campusId])->get();
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
			$userInterest 	= $user->user_interests->pluck('id')->toArray();
			$responseGroup 	= [];
			
			$groups = $this->model->with(['campus', 'user', 'group_members', 'group_feeds'])->get();
			foreach($groups as $group)
			{
				foreach($group->group_feeds as $feed)
				{
					foreach($feed->feed_interests->pluck('id') as $interest)
					{
						if(in_array($interest, $userInterest))
						{
							$responseGroup[] = $group;
						}
					}
				}
			}

			return $responseGroup;
		}

		return false;
	}
}