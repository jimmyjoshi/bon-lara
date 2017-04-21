<?php namespace App\Models\Event\Traits\Attribute;

/**
 * Trait Attribute
 *
 * @author Justin Bevan justin@smokerschoiceusa.com
 */

use File;

trait Attribute
{
	/**
	 * @return string
	 */
	public function getEditButtonAttribute()
	{
	    return '<a href="'.route('admin.event.edit', $this).'" class="btn btn-xs btn-primary"><i class="fa fa-pencil" data-toggle="tooltip" data-placement="top" title="'.trans('buttons.general.crud.edit').'"></i></a> ';
	}

	/**
	 * @return string
	 */
	public function getDeleteButtonAttribute()
	{
	    return '<a href="'.route('admin.event.destroy', $this).'"
	            data-method="delete"
	            data-trans-button-cancel="'.trans('buttons.general.cancel').'"
	            data-trans-button-confirm="'.trans('buttons.general.crud.delete').'"
	            data-trans-title="'.trans('strings.backend.general.are_you_sure').'"
	            class="btn btn-xs btn-danger"><i class="fa fa-times" data-toggle="tooltip" data-placement="top" title="'.trans('buttons.general.crud.delete').'"></i></a>';
	}

	/**
	 * @return string
	 */
	public function getActionButtonsAttribute()
	{
	    return $this->getEditButtonAttribute().
	    $this->getDeleteButtonAttribute();
	}   
}