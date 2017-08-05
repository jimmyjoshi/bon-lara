<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\ChannelTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Channel\EloquentChannelRepository;

class APIChannelController extends BaseApiController 
{   
    /**
     * Campus Transformer
     * 
     * @var Object
     */
    protected $apiTransformer;

    /**
     * Repository
     * 
     * @var Object
     */
    protected $repository;

    /**
     * __construct
     * 
     * @param apiTransformer $apiTransformer
     */
    public function __construct(EloquentChannelRepository $repository, ChannelTransformer $apiTransformer)
    {
        parent::__construct();

        $this->repository       = $repository;
        $this->apiTransformer   = $apiTransformer;
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

        $allChannels = $this->repository->getChannelsByCampusId($campusId);

        if($allChannels && count($allChannels))
        {
            $responseData = $this->apiTransformer->transformChannelCollection($allChannels);

            return $this->successResponse($responseData);
        }

        $error = [
            'reason' => 'Unable to find Channel!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'No Channel Found !');
    }

    public function create(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();        
        $campusId   = $userInfo->user_meta->campus_id;
        $input      = $request->all();

        $model = $this->repository->create($input);
        if($model)
        {
            $responseData = $this->apiTransformer->createChannel($model);

            return $this->successResponse($responseData, 'Channel is Created Successfully');
        }

        $error = [
            'reason' => 'Unable to Create new Channel!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'Please Try Again !');
    }

    /**
     * Get Channels By GroupId
     * 
     * @param Request $request
     * @return json
     */
    public function getChannelsByGroupId(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $campusId   = $userInfo->user_meta->campus_id;
        if($request->get('group_id'))
        {
            $allChannels = $this->repository->getChannelsByCampusIdGroupId($campusId, $request->get('group_id'));

            if($allChannels && count($allChannels))
            {
                $responseData = $this->apiTransformer->transformChannelCollection($allChannels);

                return $this->successResponse($responseData);
            }
        }

        $error = [
            'reason' => 'Unable to find Channel!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'No Channel Found !');        
    }

    public function deleteChannel(Request $request)
    {
        if($request->get('channel_id') && $request->get('group_id'))
        {
            $userInfo = $this->getAuthenticatedUser();
            $status   = $this->repository->removeGroupChannel($userInfo, $request->get('group_id'), $request->get('channel_id'));

            if($status)
            {
                $allChannels = $this->repository->getChannelsByCampusIdGroupId($userInfo->user_meta->campus_id, $request->get('group_id'));

                $responseData = $this->apiTransformer->transformChannelCollection($allChannels);

                    return $this->successResponse($responseData);
            }
        }

        $error = [
            'reason' => 'Unable to find Channel or Unable to Remove Channel!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'Unable to find Channel or Unable to Remove Channel !');
    }
}
