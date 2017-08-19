<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\FeedsTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Feeds\EloquentFeedsRepository;

class APIFeedsController extends BaseApiController 
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
    public function __construct(EloquentFeedsRepository $repository, FeedsTransformer $apiTransformer)
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
        $allFeeds   = $this->repository->getFeedsByCampusId($campusId);

        if($allFeeds && count($allFeeds))
        {
            $responseData = $this->apiTransformer->feedTransformCollection($userInfo, $allFeeds);
            
            return $this->successResponse($responseData);
        }

        $error = [
            'reason' => 'Unable to find Feeds!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'No Feed Found !');
    }

    /**
     * List of All Campus Feeds
     * 
     * @param Request $request
     * @return json
     */
    public function getAllCampusFeeds(Request $request) 
    {
        $userInfo   = $this->getAuthenticatedUser();
        $campusId   = $userInfo->user_meta->campus_id;
        $allFeeds   = $this->repository->getAllHomeFeeds($campusId);

        if($allFeeds && count($allFeeds))
        {
            $responseData = $this->apiTransformer->homeFeedTransformCollection($allFeeds);
            
            return $this->successResponse($responseData);
        }

        $error = [
            'reason' => 'Unable to find Feeds!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'No Feed Found !');
    }

    /**
     * Create
     * 
     * @param Request $request
     * @return json
     */
    public function create(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $campusId   = $userInfo->user_meta->campus_id;
        $input      = $request->all();

        if($request->file('attachment'))
        {
            $attachment  = rand(11111, 99999) . '_feed_attachment.' . $request->file('attachment')->getClientOriginalExtension();
            $path = base_path() . '/public/feeds/'.$userInfo->id.'/';
            if(! is_dir($path))
            {
                mkdir($path, 0777, true);
            }
            
            $request->file('attachment')->move($path, $attachment);
            $input = array_merge($input, ['is_attachment' => 1, 'attachment' => $attachment]);
        }

        $input = array_merge($input, ['campus_id' => $campusId, 'user_id' => $userInfo->id]);
        $feed = $this->repository->create($input);

        if($feed)
        {
            $responseData = $this->apiTransformer->transform($feed);
            
            return $this->successResponse($responseData);
        }

        $error = [
            'reason' => 'Unable to Create New Feed!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'Unable To Create New Feed !');
    }
    
    /**
     * Get Channel Feeds
     * 
     * @param Request $request
     * @return json
     */
    public function getChannelFeeds(Request $request)
    {
        if($request->get('channel_id'))
        {
            $userInfo   = $this->getAuthenticatedUser();
            $allFeeds   = $this->repository->getFeedsByChannelId($request->get('channel_id'));

            if($allFeeds && count($allFeeds))
            {
                $responseData = $this->apiTransformer->feedTransformCollection($userInfo, $allFeeds);
                $responseData = $this->apiTransformer->feedTransformCollectionTimeline($responseData);
                
                return $this->successResponse($responseData);
            }
        }

        $error = [
            'reason' => 'Unable to find Feeds!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'No Feed Found !');        
    }

    /**
     * Destroy
     * 
     * @param Request $request
     * @return json
     */
    public function destroy(Request $request)
    {
        if($request->get('feed_id'))
        {
            $userInfo = $this->getAuthenticatedUser();
            $campusId = $userInfo->user_meta->campus_id;
            $status   = $this->repository->destroy($userInfo, $request->get('feed_id'));

            if($status)
            {
                $allFeeds       = $this->repository->getFeedsByCampusId($campusId);
                $responseData   = $this->apiTransformer->feedTransformCollection($allFeeds);
                
                return $this->successResponse($responseData);
            }
        }

        $error = [
            'reason' => 'Unable to Delete Feed!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'Unable to Delete Feed!');        
    }

    /**
     * Report Feed
     * 
     * @param Request $request
     * @return json
     */
    public function reportFeed(Request $request)
    {
        if($request->get('feed_id'))
        {
            $userInfo = $this->getAuthenticatedUser();
            $status   = $this->repository->feedReport($userInfo, $request->get('feed_id'));

            if($status)
            {
                 $responseData = [
                    'success' => 'Feed Reported Successfully !'
                ];

                return $this->successResponse($responseData, 'Feed Reported Successfully !');
            }
        }

        $error = [
            'reason' => 'Unable to Report Feed!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'Unable to Report Feed!'); 
    }
}
