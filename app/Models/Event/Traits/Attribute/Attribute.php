<?php namespace App\Models\Event\Traits\Attribute;

/**
 * Trait Attribute
 *
 * @author Justin Bevan justin@smokerschoiceusa.com
 */

use File;
use App\Repositories\Event\EloquentEventRepository;

trait Attribute
{
	/**
	 * @return string
	 */
	public function getEditButtonAttribute($routes)
	{
		return '<a href="'.route($routes->editRoute, $this).'" class="btn btn-xs btn-primary"><i class="fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit"></i></a> ';
	}

	/**
	 * @return string
	 */
	public function getDeleteButtonAttribute($routes)
	{
	    return '<a href="'.route($routes->deleteRoute, $this).'"
	            data-method="delete"
	            data-trans-button-cancel="Cancel"
	            data-trans-button-confirm="Delete"
	            data-trans-title="Do you want to Delete this Item ?"
	            class="btn btn-xs btn-danger"><i class="fa fa-times" data-toggle="tooltip" data-placement="top" title="Delete"></i></a>';
	}

	/**
	 * @return string
	 */
	public function getActionButtonsAttribute()
	{
		$repository = new EloquentEventRepository;

		$routes = $repository->getModuleRoutes();

		return $this->getEditButtonAttribute($routes) . $this->getDeleteButtonAttribute($routes);
	}   
}