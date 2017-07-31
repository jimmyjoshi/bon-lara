<?php
namespace App\Http\Controllers\Backend\Group;

/**
 * Class AdminGroupController
 *
 * @author Anuj Jaha er.anujjaha@gmail.
 */

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Repositories\Group\EloquentGroupRepository;
use App\Repositories\Campus\EloquentCampusRepository;

class AdminGroupController extends Controller
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
    protected $createSuccessMessage = "Group Created Successfully!";

    /**
     * Edit Success Message
     * 
     * @var string
     */
    protected $editSuccessMessage = "Group Edited Successfully!";

    /**
     * Delete Success Message
     * 
     * @var string
     */
    protected $deleteSuccessMessage = "Group Deleted Successfully";

    public $groupLeaders = [];

	/**
	 * __construct
	 * 
	 * @param EloquentGroupRepository $groupRepository
	 */
	public function __construct(EloquentGroupRepository $groupRepository,  EloquentCampusRepository $campusRepository)
	{
		$this->repository          = $groupRepository;
        $this->campusRepository    = $campusRepository;
	}
        
    /**
     * Show
     * @param int $id
     * @param Request $request
     * @return view
     */
    public function show($id, Request $request)
    {
        $model = $this->repository->getById($id);

        return view($this->repository->setAdmin(true)->getModuleView('showView'))->with(
            [
                'item'          => $model,
                'repository'    => $this->repository
            ]);
    }

    /**
     * Event Listing 
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
     * Event View
     * 
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view($this->repository->setAdmin(true)->getModuleView('createView'))->with([
            'repository'        => $this->repository,
            'campusRepository'  => $this->campusRepository
        ]);
    }

    /**
     * Store View
     * 
     * @return \Illuminate\View\View
     */
    public function store(Request $request)
    {
        $imageName  = rand(11111, 99999) . '_group.' . $request->file('image')->getClientOriginalExtension();
        $status     = $request->file('image')->move(base_path() . '/public/groups/', $imageName);

        if($status)
        {
            $input = array_merge($request->all(), ['image' => $imageName]);
            
            $this->repository->create($input);

            return redirect()->route($this->repository->setAdmin(true)->getActionRoute('listRoute'))->withFlashSuccess($this->createSuccessMessage);
        }

        return redirect()->route($this->repository->setAdmin(true)->getActionRoute('listRoute'))->withFlashDanger($this->failureMessage);
    }

    /**
     * Event View
     * 
     * @return \Illuminate\View\View
     */
    public function edit($id, Request $request)
    {
        $group = $this->repository->findOrThrowException($id);

        return view($this->repository->setAdmin(true)->getModuleView('editView'))->with([
            'item'              => $group,
            'repository'        => $this->repository,
            'campusRepository'  => $this->campusRepository
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
     * Event Update
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
            ->escapeColumns(['username', 'sort'])
            ->escapeColumns(['title', 'sort'])
            ->addColumn('start_date', function ($event) {
                return date('m-d-Y', strtotime($event->start_date));
            })
            ->addColumn('group_leader', function ($group)
            {
                $groupLeaders = $group->getLeaders()->filter(function($item)
                {
                    return $this->groupLeaders[] = $item->name;

                });

                return implode(', ', $this->groupLeaders);
            })
             ->addColumn('members_count', function ($group)
            {
                return '<span class="btn btn-primary group-members" data-group-id="'.$group->id.'" >' . count($group->group_members) . '</span>';
            })
		    ->escapeColumns(['start_date', 'sort'])
		    ->escapeColumns(['end_date', 'sort'])
		    ->addColumn('actions', function ($event) {
                return $event->admin_action_buttons;
            })
		    ->make(true);
    }

    /**
     * Get Group Members
     * 
     * @param Request $request
     * @return json
     */
    public function getGroupMembers(Request $request)
    {
        if($request->get('groupId'))
        {
            $members = $this->repository->getGroupMembers($request->get('groupId'));

            if($members && count($members))
            {
                return response()->json((object) [
                    'status'    => true,
                    'members'   => $members
                ], 200);
            }
        }

        return response()->json((object) ['status' => false], 200);
    }
}
