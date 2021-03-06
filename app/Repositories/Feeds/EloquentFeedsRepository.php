<?php namespace App\Repositories\Feeds;

use App\Models\Feeds\Feeds;
use App\Models\Feeds\FeedReport;
use App\Models\Access\User\UserToken;
use  App\Models\Access\User\UserInterest;
use App\Models\Feeds\FeedInterests;
use App\Repositories\DbRepository;
use App\Exceptions\GeneralException;
use App\Library\Push\PushNotification;
use App\Models\Group\GroupMember;

class EloquentFeedsRepository extends DbRepository
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
	public $moduleTitle = 'Feeds';

	/**
	 * Table Headers
	 *
	 * @var array
	 */
	public $tableHeaders = [
		'description' 			=> 'Description',
		'actions' 				=> 'Actions'
	];

	/**
	 * Table Columns
	 *
	 * @var array
	 */
	public $tableColumns = [
		'description' =>	[
			'data' 			=> 'description',
			'name' 			=> 'description',
			'searchable' 	=> true, 
			'sortable'		=> true
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
		'listRoute' 	=> 'feeds.index',
		'createRoute' 	=> 'feeds.create',
		'storeRoute' 	=> 'feeds.store',
		'editRoute' 	=> 'feeds.edit',
		'updateRoute' 	=> 'feeds.update',
		'deleteRoute' 	=> 'feeds.destroy',
		'dataRoute' 	=> 'feeds.get-list-data'
	];

	/**
	 * Module Views
	 * 
	 * @var array
	 */
	public $moduleViews = [
		'listView' 		=> 'feeds.index',
		'createView' 	=> 'feeds.create',
		'editView' 		=> 'feeds.edit',
		'deleteView' 	=> 'feeds.destroy',
	];

	/**
	 * Construct
	 *
	 */
	public function __construct()
	{
		$this->model = new Feeds;
	}

	/**
	 * Create Campus
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
				$this->addFeedInterests($model, $input);
			}
			
			// Send Gropu Feed Push Notification	
			//$this->sendGroupFeedPushNotification($model);
			
			if(isset($input['is_campus_feed']) && $input['is_campus_feed'] == 1)
			{
				$this->sendHomeFeedPushNotification($model);
			}

			return $model->with(['campus', 'channel', 'group', 'user', 'feed_interests'])->where(['id' => $model->id])->first();
		}

		return false;
	}	

	public function sendHomeFeedPushNotification($model = null)
	{
		if($model)
		{
			if(isset($model->group_id))
			{
				$groupMemberIds = GroupMember::where(['group_id' => $model->group->id, 'status' => 1])->pluck('user_id');
				$users 			= UserToken::whereIn('user_id', $groupMemberIds)->get();
				
				foreach($users as $user)
				{
					$payload = [
						'mtitle' 	=> 'BonFire',
			            'mdesc' 	=> $model->description . ' Posted By '.$model->user->name
					];
					PushNotification::iOS($payload, $user->token);
			    }

			    return true;
			}
			else
			{
				$campusId 		= $model->campus_id;
				$tokenUserIds 	= [];
				$feedInterests 	= $model->feed_interests()->pluck('interest_id')->toArray();

				if(isset($feedInterests) && count($feedInterests))
				{
					$users = UserInterest::whereIn('interest_id', $feedInterests)->get()->filter(function($item) use($campusId)
						{
							if(isset($item->user->user_meta) && $campusId == $item->user->user_meta->campus_id)
							{
								return $item;
							}
						});

					foreach($users as $user)
					{
						if(isset($user->user->user_token))
						{
							$payload = [
								'mtitle' 	=> 'BonFire',
					            'mdesc' 	=> $model->description . ' Posted By '.$model->user->name
							];
							PushNotification::iOS($payload, $user->user->user_token->token);
						}
					}
				}

			}
		}
	}

	/**
	 * Create Campus Feeds
	 * 
	 * @param array $input
	 * @return bool
	 */
	public function createCampusFeeds($input = array())
	{
		$input = $this->prepareInputData($input, true);
		$model = $this->model->create($input);

		if($model)
		{
			if(isset($input['interests']) && count($input['interests']))
			{
				$this->addFeedInterests($model, $input);
			}

			$this->sendCampusFeedPushNotification($model);
			
			return $model->with(['campus', 'channel', 'group', 'user', 'feed_interests'])->where(['id' => $model->id])->first();
		}

		return false;
	}

	/**
	 * Send Campus Feed PushNotification
	 * 
	 * @param object $model
	 * @return bool
	 */
	public function sendCampusFeedPushNotification($model = null)
	{
		$users = UserToken::where('campus_id', $model->campus_id)->get();

		foreach($users as $user)
		{
			$payload = [
				'mtitle' 	=> 'BonFire',
	            'mdesc' 	=> $model->description . ' Posted By '.$model->user->name
			];

	        PushNotification::iOS($payload, $user->token);
	    }

	    return true;
	}

	/**
	 * Send Group Feed Push Notification
	 * 
	 * @param object $model
	 * @return bool
	 */
	public function sendGroupFeedPushNotification($model = null)
	{
		if($model)
		{
			$groupMemberIds = GroupMember::where(['group_id' => $model->group->id, 'status' => 1])->pluck('user_id');
			$users 			= UserToken::whereIn('user_id', $groupMemberIds)->get();
			
			foreach($users as $user)
			{
				$payload = [
					'mtitle' 	=> 'BonFire',
		            'mdesc' 	=> $model->description . ' Posted By '.$model->user->name
				];

				PushNotification::iOS($payload, $user->token);
		    }
		}

	    return true;
	}

	/**
	 * Add FeedInterests
	 * 
	 * @param object $model
	 * @param array $input
	 */
	public function addFeedInterests($model = null, $input = array())
	{
		if(isset($input['interests']))	
		{
			$interests = is_array($input['interests']) ? $input['interests'] : explode(',', $input['interests']);
			$feedInterests = [];

			foreach($interests as $interest)
			{
				$feedInterests[] = [
					'feed_id'		=> $model->id,
					'interest_id'	=> $interest
				];
			}

			return FeedInterests::insert($feedInterests);
		}

		return false;
	}

	/**
	 * Update Campus
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
     * Get All
     *
     * @param string $orderBy
     * @param string $sort
     * @return mixed
     */
    public function getAll($orderBy = 'id', $sort = 'asc')
    {
        return $this->model->all();
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
			$this->model->getTable().'.description',
		];
    }

    /**
     * @return mixed
     */
    public function getForDataTable()
    {
    	return  $this->model->select($this->getTableFields())->get();
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
     * Get Feeds By CampusId
     * 
     * @param int $campusId
     * @return object
     */
    public function getFeedsByCampusId($campusId = null)
    {
    	if($campusId)
    	{
    		return $this->model->with(['campus', 'channel', 'group', 'user', 'feed_interests'])->where([
    			'campus_id' 		=> $campusId,
    			'is_campus_feed' 	=> 0
    			])->orderBy('id', 'desc')->get();
    	}

    	return false;
    }

    /**
     * Get Feeds By CampusId
     * 
     * @param int $campusId
     * @return object
     */
    public function getAllHomeFeeds($user = null, $campusId = null)
    {
    	if($campusId && $user)
    	{
    		$userInterest 	= $user->user_interests()->pluck('interest_id')->toArray();
	    	$userGroups 	= GroupMember::where(['user_id' => $user->id, 'status' => 1])->pluck('group_id')->toArray();
	    	$homeFeedIds 	= [];

			$allFeeds = $this->model->where([
    			'campus_id' 		=> $campusId,
    			'is_campus_feed' 	=> 1
    			])
			->orderBy('id', 'desc')->get();


			foreach($allFeeds as $campusFeed)
			{
				if($campusFeed->is_announcement == 1)
				{
					$homeFeedIds[] = $campusFeed->id;
					continue;
				}

				if(in_array($campusFeed->group_id, $userGroups))
				{
					$homeFeedIds[] = $campusFeed->id;
				}

				$feedIntersts = $campusFeed->feed_interests()->get()->toArray();

				if($feedIntersts && count($feedIntersts))
				{
	    			foreach($feedIntersts as $finterest)
	    			{
	    				if(in_array($finterest['id'], $userInterest))
	    				{
	    					$homeFeedIds[] = $campusFeed->id;
	    					break;		
	    				}
	    			}		
					continue;
				}
			}
			
			return  $this->model->with(['campus', 'user', 'feed_interests'])->whereIn('id', $homeFeedIds)->orderBy('id', 'desc')->get();

    		$feedIds = [];

    		foreach($feeds as $feed)
    		{
                if($feed->is_announcement == 1)
                {
                	$feedIds[] = $feed->id;
                    continue;
                }

    			$feedIntersts = $feed->feed_interests()->get()->toArray();

    			foreach($feedIntersts as $finterest)
    			{
    				if(in_array($finterest['id'], $userInterest))
    				{
    					$feedIds[] = $feed->id;
    					break;		
    				}
    			}

    			if(in_array($feed->group_id, $groupIds))	
    			{
    				$feedIds[] = $feed->id;
    				continue;
    			}

    		}

    		return  $this->model->with(['campus', 'user', 'feed_interests'])->whereIn('id', $feedIds)->orderBy('id', 'desc')->get();
    	}

    	return false;
    }
    

    /**
     * Get Feeds By ChannelId
     * 
     * @param int $channelId
     * @return array|bool|mixed
     */
    public function getFeedsByChannelId($channelId = null)
    {
    	if($channelId)
    	{
    		return $this->model->with(['campus', 'channel', 'group', 'user', 'feed_interests'])->where([
    			'channel_id' 		=> $channelId,
    			'is_campus_feed' 	=> 0
    			])->get();
    	}

    	return false;
    }

    /**
     * Destroy
     * 
     * @param object $user
     * @param int $feedId
     * @return bool
     */
    public function destroy($user = null, $feedId = null)
    {
    	if($user && $feedId)
    	{
    		return $this->model->where(['id' => $feedId, 'user_id' => $user->id])->delete();
    	}

    	return false;
    }

    /**
     * FeedReport
     * 
     * @param object $user
     * @param int $feedId
     * @return bool
     */
    public function feedReport($user = null, $feedId = null, $notes = 'Feed Report')
    {
    	if($user && $feedId)
    	{
    		$feed = $this->model->find($feedId);

    		if($feed && $feed->campus_id == $user->user_meta->campus_id)
    		{
    			FeedReport::create([
    				'user_id' 		=> $user->id,
    				'feed_id'		=> $feedId,
    				'description' 	=> $notes
    			]);

    			$feedCount = $feed->is_reported + 1;

    			$feed->is_reported = $feedCount;
    			return $feed->save();
    		}
    	}

    	return false;
    }
}