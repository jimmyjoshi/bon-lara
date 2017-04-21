<?php

namespace App\Http\Controllers\Backend\Event;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Repositories\Event\EloquentEventRepository;

/**
 * Class AdminEventController
 */
class AdminEventController extends Controller
{
	/**
	 * Event Repository
	 * 
	 * @var object
	 */
	public $repository;

	/**
	 * __construct
	 * 
	 * @param EloquentEventRepository $eventRepository
	 */
	public function __construct(EloquentEventRepository $eventRepository)
	{
		$this->repository = $eventRepository;
	}

    /**
     * Event Listing 
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
    	return view('backend.event.index');
    }

    /**
     * Event View
     * 
     * @return \Illuminate\View\View
     */
    public function edit($id, Request $request)
    {
    	echo hasher()->encode('1');
    	die;
    	$event = $this->repository->findOrThrowException($id);

    	dd($event);
    	return view('backend.event.index');
    }

  	/**
     * Get Table Data
     *
     * @return json|mixed
     */
    public function getTableData()
    {
    	return Datatables::of($this->repository->getForDataTable())
		    ->escapeColumns(['name', 'sort'])
		    ->escapeColumns(['title', 'sort'])
		     ->addColumn('start_date', function ($event) {
                return date('m-d-Y', strtotime($event->start_date));
            })
		    ->escapeColumns(['start_date', 'sort'])
		    ->escapeColumns(['end_date', 'sort'])
		    ->addColumn('actions', function ($event) {
                return $event->action_buttons;
            })
		    ->make(true);
    }
}
