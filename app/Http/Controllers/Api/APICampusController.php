<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\CampusTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Campus\EloquentCampusRepository;

class APICampusController extends BaseApiController 
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
    public function __construct(EloquentCampusRepository $repository, CampusTransformer $apiTransformer)
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
        $allCampus = $this->repository->getAll()->toArray();

        if($allCampus && count($allCampus))
        {
            $responseData = $this->apiTransformer->transformCollection($allCampus);

            return $this->successResponse($responseData);
        }

        $error = [
            'reason' => 'Unable to find Campus!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'No Campus Found !');
    }
}
