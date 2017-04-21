<?php

namespace App\Http\Controllers\Backend\Event;

use App\Http\Controllers\Controller;
use App\Repositories\Event\EloquentEventRepository;
use Yajra\Datatables\Facades\Datatables;

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

	public function __construct(EloquentEventRepository $eventRepository)
	{
		$this->repository = $eventRepository;
	}

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
    	return view('backend.event.index');
    }

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
