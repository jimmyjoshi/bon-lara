<?php namespace App\Repositories\Event;

use App\Models\Event\Event;
use App\Models\Access\User\User;
use App\Repositories\DbRepository;
use App\Exceptions\GeneralException;

class EloquentEventRepository extends DbRepository implements EventRepositoryContract
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
	public $moduleTitle = 'Event';

	/**
	 * Table Headers
	 *
	 * @var array
	 */
	public $tableHeaders = [
		'Event Name',
		'User Name',
		'Title',
		'Start Date',
		'End Date',
		'Actions'
	];

	/**
	 * Table Columns
	 *
	 * @var array
	 */
	public $tableColumns = [
		[
			'data' 			=> 'name',
			'name' 			=> 'name',
			'searchable' 	=> true, 
			'sortable'		=> true
		],
		[
			'data' 			=> 'username',
			'name' 			=> 'username',
			'searchable' 	=> true, 
			'sortable'		=> true
		],
		[
			'data' 			=> 'title',
			'name' 			=> 'title',
			'searchable' 	=> true, 
			'sortable'		=> true
		],
		[
			'data' 			=> 'start_date',
			'name' 			=> 'start_date',
			'searchable' 	=> false, 
			'sortable'		=> false
		],
		[
			'data' 			=> 'end_date',
			'name' 			=> 'end_date',
			'searchable' 	=> false, 
			'sortable'		=> false
		],
		[
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
	public $isAdmin = false;

	/**
	 * Table Fields
	 * 
	 * @var array
	 */
	public $tableFields = [
	];

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
	public $clientRoutePrefix = 'client';

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
		'listRoute' 	=> 'event.index',
		'createRoute' 	=> 'event.create',
		'storeRoute' 	=> 'event.store',
		'editRoute' 	=> 'event.edit',
		'updateRoute' 	=> 'event.update',
		'deleteRoute' 	=> 'event.destroy',
		'dataRoute' 	=> 'event.get-list-data'
	];

	/**
	 * Module Views
	 * 
	 * @var array
	 */
	public $moduleViews = [
		'listView' 		=> 'event.index',
		'createView' 	=> 'event.create',
		'editView' 		=> 'event.edit',
		'deleteView' 	=> 'event.destroy',
	];

	/**
	 * Construct
	 *
	 */
	public function __construct()
	{
		$this->model 		= new Event;
		$this->userModel 	= new User;
	}

	/**
	 * Create Video
	 *
	 * @param array $input
	 * @return mixed
	 */
	public function create($input)
	{
		$input = $this->prepareInputData($input, true);
		
		return $this->model->create($input);
	}	

	/**
	 * Update Video
	 *
	 * @param int $id
	 * @param array $input
	 * @return bool|int|mixed
	 */
	public function update($id, $input)
	{
		$model = $this->findOrThrowException($id);
		$input = $this->prepareInputData($input);		
		
		return $model->update($input);
	}

	/**
	 * Destroy Video
	 *
	 * @param int $id
	 * @return mixed
	 * @throws GeneralException
	 */
	public function destroy($id)
	{
		$model = $this->findOrThrowException($id);

		if($model)
		{
			return $model->delete();
		}

		throw new GeneralException("Unable to Delete Record !");
	}

	/**
     * Get All
     *
     * @param object $videos [all videos]
     * @param boolean $hashed
     * @return mixed
     */
    public function getAll($orderBy = 'id', $sort = 'asc')
    {
        return $this->model->all();
    }

	/**
     * Get by Id
     *
     * @param object $videos [all videos]
     * @param boolean $hashed
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
			$this->model->getTable().'.title',
			$this->model->getTable().'.start_date',
			$this->model->getTable().'.end_date',
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

    	if(isset($input['start_date']) && isset($input['end_date']))
    	{
    		$input['start_date'] 	= date('Y-m-d', strtotime($input['start_date']));
    		$input['end_date'] 		= date('Y-m-d', strtotime($input['end_date']));

    		return $input;
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
    	return json_encode($this->tableHeaders);
    }

    /**
     * Get Table Columns
     *
     * @return string
     */
    public function getTableColumns()
    {
    	return json_encode($this->tableColumns);
    }

    /**
     * Get Module Routes
     * 
     * @return object
     */
    public function getModuleRoutes()
    {
    	return (object) $this->moduleRoutes;
    }

    /**
     * Get Route
     * 
     * @return object
     */
    public function getActionRoute($action = 'createRoute', $isAdmin = true)
    {
    	if($isAdmin )
    	{
    		return $this->adminRoutePrefix. '.' .$this->moduleRoutes[$action];
    	}

    	return $this->clientRoutePrefix. '.' .$this->moduleRoutes[$action];
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
     * Get Route
     * 
     * @return object
     */
    public function getModuleView($view = 'createView')
    {
    	if($this->isAdmin)
    	{
    		return $this->adminViewPrefix . '.' .$this->moduleViews[$view];
    	}

    	return $this->clientViewPrefix. '.' .$this->moduleViews[$view];
    }
}