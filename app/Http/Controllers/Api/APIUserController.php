<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\CampusTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Backend\Access\User\UserRepository;
use App\Models\Access\User\User;
use App\Models\Access\User\UserMeta;
use App\Http\Transformers\UserTransformer;

class APIUserController extends BaseApiController 
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
    public function __construct(UserRepository $repository, UserTransformer $apiTransformer)
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
    public function profile($id, Request $request) 
    {
        $user = $this->repository->getById($id);

        if($user)
        {
            $responseData = $this->apiTransformer->userDetail($user);
            if($responseData)
            {
                return $this->successResponse($responseData);
            }
        }

        $error = [
            'reason' => 'Unable to find User!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'No User Found !');
    }
}
