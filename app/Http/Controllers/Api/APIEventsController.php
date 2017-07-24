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
        $userInfo   = $this->getAuthenticatedUser();
        $campusId   = $userInfo->user_meta->campus_id;
        $events     = $this->repository->getAllEventsByCampusId($campusId);

        if($events && count($events))
        {
            $response = $this->eventTransformer->getAllEvents($events, $userInfo);
            return $this->successResponse($response);
        }

        $error = [
            'reason' => 'Unable to find Events!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'No Events Found !');
    }

    /**
     * List of All Group Events
     * 
     * @param Request $request
     * @return json
     */
    public function getGroupEvents(Request $request) 
    {
        if($request->get('group_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $groupId    = $request->get('group_id');
            $events     = $this->repository->getAllEventsByGroupId($groupId);

            if($events && count($events))
            {
                $response = $this->eventTransformer->getAllEvents($events, $userInfo);
                return $this->successResponse($response);
            }
        }

        $error = [
            'reason' => 'Unable to find Group Events!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'No Group Events Found !');
    }

    /**
     * Create
     * 
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        $input      = $request->all();
        $userInfo   = $this->getAuthenticatedUser();
        $input      = array_merge($input, ['campus_id' => $userInfo->user_meta->campus_id, 'user_id' => $userInfo->id]);
        $model      = $this->repository->create($input);

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

    /**
     * Edit
     * 
     * @param Request $request
     * @return string
     */
    public function edit(Request $request)
    {
        $eventId    = (int) $request->event_id;
        $model      = $this->repository->update($eventId, $request->all());

        if($model)
        {
            $eventData      = $this->repository->getById($eventId);
            $responseData   = $this->eventTransformer->transform($eventData);

            return $this->successResponse($responseData, 'Event is Edited Successfully');
        }

        $error = [
            'reason' => 'Invalid Inputs'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'Something went wrong !');
    }

    /**
     * Delete
     * 
     * @param Request $request
     * @return string
     */
    public function delete(Request $request)
    {
        $eventId    = (int) $request->event_id;
        $userInfo   = $this->getAuthenticatedUser();

        if($eventId)
        {
            $status = $this->repository->destroy($eventId, $userInfo);

            if($status)
            {
                $responseData = [
                    'success' => 'Event Deleted'
                ];

                return $this->successResponse($responseData, 'Event is Deleted Successfully');
            }
        }

        $error = [
            'reason' => "You don't have permission to Delete Event!"
        ];

        return $this->setStatusCode(404)->failureResponse($error, 'Something went wrong !');
    }

    /**
     * Join Event
     * 
     * @param Request $request
     * @return json
     */
    public function joinEvent(Request $request)
    {
        $eventId    = (int) $request->event_id;
        $userInfo   = $this->getAuthenticatedUser();

        if($eventId)
        {
            $status = $this->repository->joinEvent($eventId, $userInfo);

            if($status)
            {
                $responseData = [
                    'success' => 'Event Joined Successfully.'
                ];

                return $this->successResponse($responseData, 'Event added to your Calendar.');
            }
        }

        $error = [
            'reason' => "Already Event Join or Event is not Exists!"
        ];

        return $this->setStatusCode(404)->failureResponse($error, 'Something went wrong !');
    }

    /**
     * Skip Event
     * 
     * @param Request $request
     * @return json
     */
    public function skipEvent(Request $request)
    {
        $eventId    = (int) $request->event_id;
        $userInfo   = $this->getAuthenticatedUser();

        if($eventId)
        {
            $status = $this->repository->removeEventMember($eventId, $userInfo);

            if($status)
            {
                $responseData = [
                    'success' => 'Exit from Event Successfully.'
                ];

                return $this->successResponse($responseData, 'Event removed From your Calendar.');
            }
        }

        $error = [
            'reason' => "Event is not Exists or Deleted!"
        ];

        return $this->setStatusCode(404)->failureResponse($error, 'Something went wrong !');
    }
}
