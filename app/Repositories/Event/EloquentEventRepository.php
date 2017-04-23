<?php namespace App\Repositories\Event;

use App\Repositories\DbRepository;
use App\Models\Event\Event;
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
	 * Construct
	 *
	 */
	public function __construct()
	{
		$this->model = new Event;
	}

	/**
	 * Create Video
	 *
	 * @param array $input
	 * @return mixed
	 */
	public function create($input)
	{
		$input = $this->prepareInputData($input);

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

		throw new GeneralException(trans('exceptions.backend.access.roles.delete_error'));
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
     * @return mixed
     */
    public function getForDataTable()
    {
        return $this->model->select(['id', 'name', 'start_date', 'title', 'end_date']);
    }

    /**
     * Prepare Input Data
     * 
     * @param array $input
     * @return array
     */
    public function prepareInputData($input = array())
    {
    	if(isset($input['start_date']) && isset($input['end_date']))
    	{
    		$input['start_date'] = date('Y-m-d', strtotime($input['start_date']));
    		$input['end_date'] = date('Y-m-d', strtotime($input['end_date']));

    		return $input;
    	}

    	return $input;
    }
}