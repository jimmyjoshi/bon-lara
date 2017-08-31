<?php

namespace App\Http\Controllers\Backend\Feeds;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Repositories\Feeds\EloquentFeedsRepository;
use App\Repositories\Campus\EloquentCampusRepository;
use App\Repositories\Interest\EloquentInterestRepository;

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
        $this->repository           = new EloquentFeedsRepository;
        $this->campusRepository     = new EloquentCampusRepository;
        $this->interestRepository   = new EloquentInterestRepository;
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
            'repository'        => $this->repository,
            'campusRepository'  => $this->campusRepository,
            'interestRepository' => $this->interestRepository
        ]);
    }

    /**
     * Store View
     * 
     * @return \Illuminate\View\View
     */
    public function store(Request $request)
    {
        $input = $request->all();

        if($request->file('attachment'))
        {
            $attachment  = rand(11111, 99999) . '_feed_attachment.' . $request->file('attachment')->getClientOriginalExtension();
            $path = base_path() . '/public/feeds/'.access()->user()->id.'/';
            if(! is_dir($path))
            {
                mkdir($path, 0777, true);
            }
            
            $request->file('attachment')->move($path, $attachment);

            $input = array_merge($input, ['is_attachment' => 1, 'attachment' => $attachment]);
        }

        $input = array_merge($input, ['is_campus_feed' => 1, 'is_announcement' => 1]);
        
        $this->repository->createCampusFeeds($input);

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
