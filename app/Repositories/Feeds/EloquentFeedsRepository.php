<?php namespace App\Repositories\Feeds;

use App\Models\Feeds\Feeds;
use App\Models\Feeds\FeedInterests;
use App\Repositories\DbRepository;
use App\Exceptions\GeneralException;

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
			if(isset($input['interests']) && count($input['interests']))
			{
				$this->addFeedInterests($model, $input);
			}

			return $model->with(['campus', 'channel', 'group', 'user', 'feed_interests'])->first();
		}

		return false;
	}	

	/**
	 * Add FeedInterests
	 * 
	 * @param object $model
	 * @param array $input
	 */
	public function addFeedInterests($model = null, $input = array())
	{
		if(isset($input['interests']) && count($input['interests']))	
		{
			$feedInterests = [];

			foreach($input['interests'] as $interest)
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
	 * Destroy Campus
	 *
	 * @param int $id
	 * @return mixed
	 * @throws GeneralException
	 */
	public function destroy($id)
	{
		$model = $this->model->find($id);
			
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
    		return $this->model->with(['campus', 'channel', 'group', 'user', 'feed_interests'])->where(['campus_id' => $campusId])->orderBy('id', 'desc')->get();
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
    		return $this->model->with(['campus', 'channel', 'group', 'user', 'feed_interests'])->where(['channel_id' => $channelId])->orderBy('id', 'desc')->get();
    	}

    	return false;
    }
}