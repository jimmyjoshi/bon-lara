<?php namespace App\Repositories\Campus;

use App\Models\Campus\Campus;
use App\Repositories\DbRepository;
use App\Exceptions\GeneralException;

class EloquentCampusRepository extends DbRepository
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
	public $moduleTitle = 'Campus';

	/**
	 * Table Headers
	 *
	 * @var array
	 */
	public $tableHeaders = [
		'name' 					=> 'Campus Name',
		'campus_code' 			=> 'Campus Code',
		'valid_domain' 			=> 'Valid Domain',
		'contact_person_name' 	=> 'Contact Person',
		'contact_number' 		=> 'Contact Number',
		'email_id' 				=> 'Email ID',
		'actions' 				=> 'Actions'
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
		'campus_code' => [
			'data' 			=> 'campus_code',
			'name' 			=> 'campus_code',
			'searchable' 	=> true, 
			'sortable'		=> true
		],
		'valid_domain' => [
			'data' 			=> 'valid_domain',
			'name' 			=> 'valid_domain',
			'searchable' 	=> true, 
			'sortable'		=> true
		],
		'contact_person_name' => [
			'data' 			=> 'contact_person_name',
			'name' 			=> 'contact_person_name',
			'searchable' 	=> false, 
			'sortable'		=> false
		],
		'contact_number' => [
			'data' 			=> 'contact_number',
			'name' 			=> 'contact_number',
			'searchable' 	=> false, 
			'sortable'		=> false
		],
		'email_id' => [
			'data' 			=> 'email_id',
			'name' 			=> 'email_id',
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
		'listRoute' 	=> 'campus.index',
		'createRoute' 	=> 'campus.create',
		'storeRoute' 	=> 'campus.store',
		'editRoute' 	=> 'campus.edit',
		'updateRoute' 	=> 'campus.update',
		'deleteRoute' 	=> 'campus.destroy',
		'dataRoute' 	=> 'campus.get-list-data'
	];

	/**
	 * Module Views
	 * 
	 * @var array
	 */
	public $moduleViews = [
		'listView' 		=> 'campus.index',
		'createView' 	=> 'campus.create',
		'editView' 		=> 'campus.edit',
		'deleteView' 	=> 'campus.destroy',
	];

	/**
	 * Construct
	 *
	 */
	public function __construct()
	{
		$this->model 		= new Campus;
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
			return $model;
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
			$this->model->getTable().'.name',
			$this->model->getTable().'.campus_code',
			$this->model->getTable().'.valid_domain',
			$this->model->getTable().'.contact_person_name',
			$this->model->getTable().'.contact_number',
			$this->model->getTable().'.email_id'
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
}