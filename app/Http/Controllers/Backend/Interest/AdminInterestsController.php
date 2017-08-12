<?php

namespace App\Http\Controllers\Backend\Interest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Repositories\Interest\EloquentInterestRepository;
use Html;

/**
 * Class AdminInterestsController
 */
class AdminInterestsController extends Controller
{
	/**
	 * Interest Repository
	 * 
	 * @var object
	 */
	public $repository;

    /**
     * Create Success Message
     * 
     * @var string
     */
    protected $createSuccessMessage = "Interest Created Successfully!";

    /**
     * Failure Message
     * 
     * @var string
     */
    protected $failureMessage = "Unable to Create/Edit Interest!";

    /**
     * Edit Success Message
     * 
     * @var string
     */
    protected $editSuccessMessage = "Interest Edited Successfully!";

    /**
     * Delete Success Message
     * 
     * @var string
     */
    protected $deleteSuccessMessage = "Interest Deleted Successfully";

	/**
	 * __construct
	 * 
	 * @param EloquentInterestRepository $interestRepository
	 */
	public function __construct(EloquentInterestRepository $interestRepository)
	{
        $this->repository = $interestRepository;
	}

    /**
     * Interest Listing 
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
     * Interest View
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
        $imageName  = rand(11111, 99999) . '_interest.' . $request->file('image')->getClientOriginalExtension();
        $status     = $request->file('image')->move(base_path() . '/public/interests/', $imageName);

        if($status)
        {
            $input = array_merge($request->all(), ['image' => $imageName]);
            
            $this->repository->create($input);

            return redirect()->route($this->repository->setAdmin(true)->getActionRoute('listRoute'))->withFlashSuccess($this->createSuccessMessage);
        }

        return redirect()->route($this->repository->setAdmin(true)->getActionRoute('listRoute'))->withFlashDanger($this->failureMessage);
    }

    /**
     * Interest View
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
        $input = $request->all();

        if($request->file('image'))
        {
            $imageName  = rand(11111, 99999) . '_interest.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(base_path() . '/public/interests/', $imageName);
            $input = array_merge($request->all(), ['image' => $imageName]);
        }

        $status = $this->repository->update($id, $input);
        
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
            ->addColumn('image', function ($interest) 
            {
                if($interest->image && file_exists(base_path() . '/public/interests/'.$interest->image))
                {
                    return  Html::image('/interests/'.$interest->image, $interest->name, ['width' => 60, 'height' => 60]);
                }
                else
                {
                    return  Html::image('/interests/default.png', $interest->name, ['width' => 60, 'height' => 60]);    
                }
            })
            ->addColumn('actions', function ($event) {
                return $event->admin_action_buttons;
            })
		    ->make(true);
    }
}
