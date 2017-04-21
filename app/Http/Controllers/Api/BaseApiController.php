<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuthExceptions\JWTException;
use App\Http\Transformers\UserTransformer;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class BaseApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * UserTransformer
     *
     * @var object
     */
    protected $userTransformer;

    /**
     * __construct
     * 
     * @param UserTransformer $userTransformer
     */
    public function __construct()
    {
        $this->userTransformer = new UserTransformer;
    }

    /**
     * default status code
     *
     * @var integer
     */
    protected $statusCode = 200;
    
    /**
     * get the status code
     *
     * @return statuscode
     */
    
    public function getStatusCode()
    {
    	return $this->statusCode;
    }

    /**
     * Get JWT Authenticated User
     *
     * @return object
     */
    public function getAuthenticatedUser()
    {
        return JWTAuth::parseToken()->authenticate();
    }

    /**
     * Get Api User Info
     * 
     * @return array
     */
    public function getApiUserInfo()
    {
        return  $this->userTransformer->getUserInfo($this->getAuthenticatedUser());
    }

    /**
     * set the status code
     *
     * @param [type] $statusCode [description]
     * @return mix
     */
    public function setStatusCode($statusCode)
    {
    	$this->statusCode = $statusCode;

    	return $this;
    }
    
    /**
     * responsd not found
     *
     * @param  string $message
     * @return mix
     */
    public function respondNotFound($message = "Not Found")
    {
    	return $this->setStatusCode(IlluminateResponse::HTTP_NOT_FOUND)->respondWithError($message);
    }

    /**
     * Respond with error
     *
     * @param  string $message
     * @return mix
     */
    public function respondInternalError($message = "Internal Error")
    {
    	return $this->setStatusCode('500')->respondWithError($message);
    }

    /**
     * Respond
     *
     * @param  array $data
     * @param  array  $headers
     * @return mix
     */
    public function respond($data, $headers = [])
    {        
    	//return Response::json($data, $this->getStatusCode(), $headers);
        return Response::json($data);
    }
    
    /**
     * Respond
     *
     * @param  array $data
     * @param  array  $headers
     * @return mix
     */
    public function ApiSuccessResponse($data = array(), $headers = [])
    {
        $response['data'] = $data;
        $response['message'] = 'Success';
        $response['code'] = $this->getStatusCode();
    	//return Response::json($response, $this->getStatusCode(), $headers);
        return Response::json($response);
    }

    /**
     * respond with pagincation
     *
     * @param Paginator $lessions
     * @param array $data
     * @return mix
     */
    public function respondWithPagination($lessions, $data)
    {
        $data = array_merge($data, [
            'paginator'   => [
                'total_count'   => $lessions->total(),
                'total_pages'   => ceil($lessions->total() / $lessions->perPage()),
                'current_page'  => $lessions->currentPage(),
                'limit'         => $lessions->perPage()
             ]
        ]);
        return $this->respond($data);
    }

    /**
     * respond with error
     *
     * @param $message
     * @return mix
     */
    public function respondWithError($message)
    {
    	return $this->respond([
    			'message' => $message,
                        'code' => $this->getStatusCode()
    		]);
    }

    /**
     * Respond Created
     *
     * @param  string $message
     * @return mix
     */
    public function respondCreated($message)
    {
        return $this->setStatusCode(201)->respond([
            'message' => $message
        ]);
    }

    /**
     * Throw Validation
     *
     * @param  string $message
     * @return mix
     */
    public function throwValidation($message)
    {
        return $this->setStatusCode(422)
                        ->respondWithError($messaege);
    }
}
