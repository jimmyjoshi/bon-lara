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

    /**
     * Update Profile
     * 
     * @param int|mixed  $userId
     * @param Request $request
     * @return json
     */
    public function updateProfile($userId = null, Request $request)
    {
        if($userId)
        {
            $user = $this->repository->getById($userId);
        }
        else
        {
            $user = $this->getAuthenticatedUser();
        }

        if($user && $request->get('name'))
        {
            $user->name = $request->get('name');
            $status = $user->save();
            if($status)
            {
                $responseData = $this->apiTransformer->userDetail($user);

                if($responseData)
                {
                    return $this->successResponse($responseData);
                }
            }
        }

        $error = [
            'reason' => 'Unable to find User!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'No User Found !');
    }

    /**
     * Add Interest
     * 
     * @param Request $request
     */
    public function addInterest(Request $request)
    {
        $user = $this->getAuthenticatedUser();

        if($user)
        {
            $input = $request->all();

            if(isset($input['interest_id']))
            {
                $status = $this->repository->addInterest($user->id, $input['interest_id']);

                if($status)
                {
                    $interests    = $this->repository->getUserInterest($user);
                    $responseData = $this->apiTransformer->userDetailWithInterest($user, $interests);
                    
                    return $this->successResponse($responseData);
                }
            }
        }

        $error = [
            'reason' => 'Unable to find User!'
        ];
        return $this->setStatusCode(400)->failureResponse($error, 'No User Found !');
    }

    /**
     * Remove Interest
     * 
     * @param Request $request
     */
    public function removeInterest(Request $request)
    {
        $user = $this->getAuthenticatedUser();

        if($user)
        {
            $input = $request->all();

            if(isset($input['interest_id']))
            {
                $status = $this->repository->removeInterest($user->id, $input['interest_id']);

                if($status)
                {
                    $interests    = $this->repository->getUserInterest($user);
                    $responseData = $this->apiTransformer->userDetailWithInterest($user, $interests);
                    
                    return $this->successResponse($responseData);
                }
            }
        }

        $error = [
            'reason' => 'Unable to find User!'
        ];
        return $this->setStatusCode(400)->failureResponse($error, 'No User Found !');
    }

    /**
     * Profile With Interest
     * 
     * @param int $userId
     * @param Request $request
     * @return json
     */
    public function profileWithInterest($userId = null, Request $request)
    {
        if($userId)
        {
            $user = $this->repository->getById($userId);
        }
        else
        {
            $user = $this->getAuthenticatedUser();
        }

        if($user)
        {
            $interests      = $this->repository->getUserInterest($user);
            $userInterests  = [];

            if($interests)
            {
                $userInterests = $interests;
            }

            $responseData   = $this->apiTransformer->userDetailWithInterest($user, $userInterests);

            return $this->successResponse($responseData);
        }

        $error = [
            'reason' => 'Unable to find User with Interest!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'No User Found !');        
    }

    public function getAllCampusUsers()
    {
        $user = $this->getAuthenticatedUser();       

        if($user)
        {
            $users          = $this->repository->getAllCampusUsers($user);   
            $responseData   = $this->apiTransformer->getAllUsers($users);

            return $this->successResponse($responseData);
        }

        $error = [
            'reason' => 'Unable to find User with Interest!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'No Users Found !');        
    }
}