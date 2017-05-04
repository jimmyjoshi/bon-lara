<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\EventTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Event\EloquentEventRepository;


class APIEventsController extends BaseApiController 
{   
    /**
     * Event Transformer
     * 
     * @var Object
     */
    protected $eventTransformer;

    /**
     * Repository
     * 
     * @var Object
     */
    protected $repository;

    /**
     * __construct
     * 
     * @param EventTransformer $eventTransformer
     */
    public function __construct(EloquentEventRepository $repository, EventTransformer $eventTransformer)
    {
        parent::__construct();

        $this->repository       = $repository;
        $this->eventTransformer = $eventTransformer;
    }

    /**
     * List of All Events
     * 
     * @param Request $request
     * @return json
     */
    public function index(Request $request) 
    {
        $userInfo   = $this->getApiUserInfo();
        $events     = $this->repository->getAll()->toArray();
        $eventsData = $this->eventTransformer->transformCollection($events);

        $responseData = array_merge($userInfo, ['events' => $eventsData]);

        return $this->successResponse($responseData);
        // if no errors are encountered we can return a JWT
        return response()->json($responseData);
    }

    /**
     * Create
     * 
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        $model = $this->repository->create($request->all());

        if($model)
        {
            $responseData = $this->eventTransformer->createEvent($model);

            return $this->successResponse($responseData, 'Event is Created Successfully');
        }

        $error = [
            'reason' => 'Invalid Inputs'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'Something went wrong !');
    }
}
