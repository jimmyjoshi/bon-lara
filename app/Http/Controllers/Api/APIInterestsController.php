<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\InterestsTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Interest\EloquentInterestRepository;

class APIInterestsController extends BaseApiController 
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
     * @param EloquentInterestRepository $repository
     * @param apiTransformer $apiTransformer
     */
    public function __construct(EloquentInterestRepository $repository, InterestsTransformer $apiTransformer)
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
        $allInterests = $this->repository->getAll()->toArray();

        if($allInterests && count($allInterests))
        {
            $responseData = $this->apiTransformer->transformCollection($allInterests);

            return $this->successResponse($responseData);
        }

        $error = [
            'reason' => 'Unable to find Interest!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'No Interest Found !');
    }
}
