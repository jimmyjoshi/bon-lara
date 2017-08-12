<?php namespace App\Models\Group\Traits\Attribute;

/**
 * Trait Attribute
 *
 * @author Anuj Jaha er.anujjaha@gmail.com
 */

use File;
use App\Repositories\Group\EloquentGroupRepository;

trait Attribute
{
	/**
	 * @return string
	 */
	public function getEditButtonAttribute($routes, $prefix = 'admin')
	{
		return '<a href="'.route($prefix .'.'. $routes->editRoute, $this).'" class="btn btn-xs btn-primary"><i class="fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit"></i></a> ';
	}

	/**
	 * @return string
	 */
	public function getViewButtonAttribute($routes, $prefix = 'admin')
	{
		return '<a href="'.route($prefix .'.'. $routes->viewRoute, $this).'" class="btn btn-xs btn-success"><i class="fa fa-eye" data-toggle="tooltip" data-placement="top" title="View"></i></a> ';
	}

	/**
	 * @return string
	 */
	public function getDeleteButtonAttribute($routes, $prefix = 'admin')
	{
		return '';
	    return '<a href="'.route($prefix .'.'. $routes->deleteRoute, $this).'"
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
		$repository = new EloquentGroupRepository;

		$routes = $repository->getModuleRoutes();

		return $this->getEditButtonAttribute($routes, $repository->clientRoutePrefix) . $this->getDeleteButtonAttribute($routes, $repository->clientRoutePrefix);
	}   

	/**
	 * @return string
	 */
	public function getAdminActionButtonsAttribute()
	{
		$repository = new EloquentGroupRepository;

		$routes = $repository->getModuleRoutes();

		return $this->getViewButtonAttribute($routes, $repository->adminRoutePrefix) . $this->getEditButtonAttribute($routes, $repository->adminRoutePrefix) . $this->getDeleteButtonAttribute($routes, $repository->adminRoutePrefix);
	}   
}