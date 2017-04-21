<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\EventTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Event\EloquentEventRepository;


class EventsController extends BaseApiController 
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
    protected $respository;

    /**
     * __construct
     * 
     * @param EventTransformer $eventTransformer
     */
    public function __construct(EloquentEventRepository $respository, EventTransformer $eventTransformer)
    {
        parent::__construct();

        $this->respository      = $respository;
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
        $events     = $this->respository->getAll()->toArray();
        $eventsData = $this->eventTransformer->transformCollection($events);

        $responseData = array_merge($userInfo, ['events' => $eventsData]);

        // if no errors are encountered we can return a JWT
        return response()->json($responseData);
    }
}
