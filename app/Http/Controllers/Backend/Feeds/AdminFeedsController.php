<?php

namespace App\Http\Controllers\Backend\Feeds;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Repositories\Feeds\EloquentFeedsRepository;

/**
 * Class AdminFeedsController
 */
class AdminFeedsController extends Controller
{
	/**
	 * Event Repository
	 * 
	 * @var object
	 */
	public $repository;

    /**
     * Create Success Message
     * 
     * @var string
     */
    protected $createSuccessMessage = "Feeds Created Successfully!";

    /**
     * Edit Success Message
     * 
     * @var string
     */
    protected $editSuccessMessage = "Feeds Edited Successfully!";

    /**
     * Delete Success Message
     * 
     * @var string
     */
    protected $deleteSuccessMessage = "Feeds Deleted Successfully";

	/**
	 * __construct
	 * 
	 * @param EloquentEventRepository $campusRepository
	 */
	public function __construct()
	{
        $this->repository = new EloquentFeedsRepository;
	}

    /**
     * Campus Listing 
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view($this->repository->setAdmin(true)->getModuleView('listView'))->with([
            'repository' => $this->repository
        ]);
    }

    /**
     * Campus View
     * 
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view($this->repository->setAdmin(true)->getModuleView('createView'))->with([
            'repository' => $this->repository
        ]);
    }

    /**
     * Store View
     * 
     * @return \Illuminate\View\View
     */
    public function store(Request $request)
    {
        $this->repository->create($request->all());

        return redirect()->route($this->repository->setAdmin(true)->getActionRoute('listRoute'))->withFlashSuccess($this->createSuccessMessage);
    }

    /**
     * Campus View
     * 
     * @return \Illuminate\View\View
     */
    public function edit($id, Request $request)
    {
        $event = $this->repository->findOrThrowException($id);

        return view($this->repository->setAdmin(true)->getModuleView('editView'))->with([
            'item'          => $event,
            'repository'    => $this->repository
        ]);
    }

    /**
     * Event Update
     * 
     * @return \Illuminate\View\View
     */
    public function update($id, Request $request)
    {
        $status = $this->repository->update($id, $request->all());
        
        return redirect()->route($this->repository->setAdmin(true)->getActionRoute('listRoute'))->withFlashSuccess($this->editSuccessMessage);
    }

    /**
     * Campus Update
     * 
     * @return \Illuminate\View\View
     */
    public function destroy($id)
    {
        $status = $this->repository->destroy($id);
        
        return redirect()->route($this->repository->setAdmin(true)->getActionRoute('listRoute'))->withFlashSuccess($this->deleteSuccessMessage);
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
            ->escapeColumns(['campus_code', 'sort'])
            ->escapeColumns(['valid_domain', 'sort'])
            ->escapeColumns(['contact_person_name', 'sort'])
            ->escapeColumns(['contact_number', 'sort'])
            ->escapeColumns(['email_id', 'sort'])
            ->addColumn('actions', function ($event) {
                return $event->admin_action_buttons;
            })
		    ->make(true);
    }
}
