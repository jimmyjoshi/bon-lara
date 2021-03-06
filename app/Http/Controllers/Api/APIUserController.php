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
            if($request->file('image'))
            {
                
                $imageName  = rand(11111, 99999) . '_profile-pic.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(base_path() . '/public/profile-pictures/', $imageName);
                $user->user_meta->profile_picture = $imageName;
                $user->user_meta->save();
            }

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

    /**
     * SetToken
     * 
     * @param Request $request
     */
    public function setToken(Request $request)
    {
        $user = $this->getAuthenticatedUser();       

        if($user && $request->get('device_token'))
        {
            $status = $this->repository->setToken($user, $request->get('device_token'));   

            if($status)
            {
                $responseData = [
                    'success' => 'Token Saved Successfully !'
                ];

                return $this->successResponse($responseData, 'Token Saved Successfully !');
            }
        }

        $error = [
            'reason' => 'Unable to Set User Token!'
        ];

        return $this->setStatusCode(400)->failureResponse($error, 'No Users Found !');   
    }

    public function addBulkInterest(Request $request)
    {
        $user = $this->getAuthenticatedUser();
        
        if($user && $request->get('interest_id'))
        {
            $status = $this->repository->addBulkInterest($user->id, $request->get('interest_id'));

            if($status)
            {
                $interests    = $this->repository->getUserInterest($user);
                $responseData = $this->apiTransformer->userDetailWithInterest($user, $interests);
                
                return $this->successResponse($responseData);
            }
        }

        $error = [
            'reason' => 'Unable to find User!'
        ];
        return $this->setStatusCode(400)->failureResponse($error, 'No User Found !');
    }

    public function removeBulkInterest(Request $request)
    {
        $user = $this->getAuthenticatedUser();

        if($user && $request->get('interest_id'))
        {
            $status = $this->repository->removeBulkInterest($user->id, $request->get('interest_id'));

            if($status)
            {
                $responseData = [
                    'success' => 'Interests Removed Successfully !'
                ];

                return $this->successResponse($responseData, 'Interests Removed Successfully !');
            }
        }

        $error = [
            'reason' => 'Unable to find User!'
        ];
        return $this->setStatusCode(400)->failureResponse($error, 'No User Found !'); 
    }

    /**
     * Report User
     * 
     * @param Request $request 
     * @return json
     */
    public function reportUser(Request $request)
    {
        $user = $this->getAuthenticatedUser();

        if($user && $request->get('report_user_id') && $user->id != $request->get('report_user_id'))
        {
            $status = $this->repository->reportUser($user, $request->get('report_user_id'), $request->get('description'));

            if($status)
            {
                $responseData = [
                    'success' => 'User Reported Successfully ! We will take action in 48 - 96 hours !'
                ];

                return $this->successResponse($responseData, 'User Reported Successfully !');
            }
        }

        $error = [
            'reason' => 'Unable to find User!'
        ];
        return $this->setStatusCode(400)->failureResponse($error, 'No User Found !');        
    }

    /**
     * Privacy Policy
     * 
     * @param Request $request
     * @return json
     */
    public function privacyPolicy(Request $request)
    {
        $responseData = [
            'link'      => 'https://bonfireapp.squarespace.com/privacy-policy',
            'message' => 'Lorem Impus Lomre Impsum Loerm Lorem Impus Lomre Impsum Loerm Lorem Impus Lomre Impsum Loerm Lorem Impus Lomre Impsum Loerm ',
            'success' => 'Privacy Policy'
        ];

        return $this->successResponse($responseData, 'Privacy Policy');
    }

    /**
     * Privacy Policy
     * 
     * @param Request $request
     * @return json
     */
    public function termsConditions(Request $request)
    {
        $responseData = [
            'link'      => 'https://bonfireapp.squarespace.com/terms',
            'message' => 'Lorem Impus Lomre Impsum Loerm Lorem Impus Lomre Impsum Loerm Lorem Impus Lomre Impsum Loerm Lorem Impus Lomre Impsum Loerm ',
            'success' => 'Terms & Conditions'
        ];

        return $this->successResponse($responseData, 'Terms & Conditions');
    }
}