<?php

namespace App\Repositories\Backend\Access\User;

use App\Models\Access\User\User;
use App\Models\Access\User\UserMeta;
use App\Models\Access\User\UserReport;
use App\Models\Access\User\UserToken;
use App\Models\Access\User\UserInterest;
use App\Models\Interest\Interest;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use App\Events\Backend\Access\User\UserCreated;
use App\Events\Backend\Access\User\UserDeleted;
use App\Events\Backend\Access\User\UserUpdated;
use App\Events\Backend\Access\User\UserRestored;
use App\Events\Backend\Access\User\UserDeactivated;
use App\Events\Backend\Access\User\UserReactivated;
use App\Events\Backend\Access\User\UserPasswordChanged;
use App\Repositories\Backend\Access\Role\RoleRepository;
use App\Events\Backend\Access\User\UserPermanentlyDeleted;
use App\Notifications\Frontend\Auth\UserNeedsConfirmation;
use App\Notifications\Frontend\Auth\UserForgotPassword;
    
/**
 * Class UserRepository.
 */
class UserRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = User::class;

    /**
     * @var RoleRepository
     */
    protected $role;

    /**
     * Model Object
     * 
     * @var object
     */
    protected $model;

    /**
     * @param RoleRepository $role
     */
    public function __construct(RoleRepository $role)
    {
        $this->role     = $role;
        $this->model    = new User;
    }

    /**
     * @param        $permissions
     * @param string $by
     *
     * @return mixed
     */
    public function getByPermission($permissions, $by = 'name')
    {
        if (! is_array($permissions)) {
            $permissions = [$permissions];
        }

        return $this->query()->whereHas('roles.permissions', function ($query) use ($permissions, $by) {
            $query->whereIn('permissions.'.$by, $permissions);
        })->get();
    }

    /**
     * @param        $roles
     * @param string $by
     *
     * @return mixed
     */
    public function getByRole($roles, $by = 'name')
    {
        if (! is_array($roles)) {
            $roles = [$roles];
        }

        return $this->query()->whereHas('roles', function ($query) use ($roles, $by) {
            $query->whereIn('roles.'.$by, $roles);
        })->get();
    }

    /**
     * @param int  $status
     * @param bool $trashed
     *
     * @return mixed
     */
    public function getForDataTable($status = 1, $trashed = false)
    {
        /**
         * Note: You must return deleted_at or the User getActionButtonsAttribute won't
         * be able to differentiate what buttons to show for each row.
         */
        $dataTableQuery = $this->query()
            ->with('roles')
            ->select([
                config('access.users_table').'.id',
                config('access.users_table').'.name',
                config('access.users_table').'.email',
                config('access.users_table').'.status',
                config('access.users_table').'.confirmed',
                config('access.users_table').'.created_at',
                config('access.users_table').'.updated_at',
                config('access.users_table').'.deleted_at',
            ]);

        if ($trashed == 'true') {
            return $dataTableQuery->onlyTrashed();
        }

        // active() is a scope on the UserScope trait
        return $dataTableQuery->active($status);
    }

    /**
     * @param Model $input
     */
    public function create($input)
    {
        $data = $input['data'];
        $roles = $input['roles'];

        $user = $this->createUserStub($data);

        DB::transaction(function () use ($user, $data, $roles) {
            if ($user->save()) {

                //User Created, Validate Roles
                if (! count($roles['assignees_roles'])) {
                    throw new GeneralException(trans('exceptions.backend.access.users.role_needed_create'));
                }

                //Attach new roles
                $user->attachRoles($roles['assignees_roles']);

                //Send confirmation email if requested
                if (isset($data['confirmation_email']) && $user->confirmed == 0) {
                    $user->notify(new UserNeedsConfirmation($user->confirmation_code));
                }

                event(new UserCreated($user));

                return true;
            }

            throw new GeneralException(trans('exceptions.backend.access.users.create_error'));
        });
    }

    /**
     * @param Model $user
     * @param array $input
     *
     * @return bool
     * @throws GeneralException
     */
    public function update(Model $user, array $input)
    {
        $data = $input['data'];
        $roles = $input['roles'];

        $this->checkUserByEmail($data, $user);

        DB::transaction(function () use ($user, $data, $roles) {
            if ($user->update($data)) {
                //For whatever reason this just wont work in the above call, so a second is needed for now
                $user->status = isset($data['status']) ? 1 : 0;
                $user->confirmed = isset($data['confirmed']) ? 1 : 0;
                $user->save();

                $this->checkUserRolesCount($roles);
                $this->flushRoles($roles, $user);

                event(new UserUpdated($user));

                return true;
            }

            throw new GeneralException(trans('exceptions.backend.access.users.update_error'));
        });
    }

    /**
     * @param Model $user
     * @param $input
     *
     * @throws GeneralException
     *
     * @return bool
     */
    public function updatePassword(Model $user, $input)
    {
        $user->password = bcrypt($input['password']);

        if ($user->save()) {
            event(new UserPasswordChanged($user));

            return true;
        }

        throw new GeneralException(trans('exceptions.backend.access.users.update_password_error'));
    }

    /**
     * @param Model $user
     *
     * @throws GeneralException
     *
     * @return bool
     */
    public function delete(Model $user)
    {
        if (access()->id() == $user->id) {
            throw new GeneralException(trans('exceptions.backend.access.users.cant_delete_self'));
        }

        if ($user->delete()) {
            event(new UserDeleted($user));

            return true;
        }

        throw new GeneralException(trans('exceptions.backend.access.users.delete_error'));
    }

    /**
     * @param Model $user
     *
     * @throws GeneralException
     */
    public function forceDelete(Model $user)
    {
        if (is_null($user->deleted_at)) {
            throw new GeneralException(trans('exceptions.backend.access.users.delete_first'));
        }

        DB::transaction(function () use ($user) {
            if ($user->forceDelete()) {
                event(new UserPermanentlyDeleted($user));

                return true;
            }

            throw new GeneralException(trans('exceptions.backend.access.users.delete_error'));
        });
    }

    /**
     * @param Model $user
     *
     * @throws GeneralException
     *
     * @return bool
     */
    public function restore(Model $user)
    {
        if (is_null($user->deleted_at)) {
            throw new GeneralException(trans('exceptions.backend.access.users.cant_restore'));
        }

        if ($user->restore()) {
            event(new UserRestored($user));

            return true;
        }

        throw new GeneralException(trans('exceptions.backend.access.users.restore_error'));
    }

    /**
     * @param Model $user
     * @param $status
     *
     * @throws GeneralException
     *
     * @return bool
     */
    public function mark(Model $user, $status)
    {
        if (access()->id() == $user->id && $status == 0) {
            throw new GeneralException(trans('exceptions.backend.access.users.cant_deactivate_self'));
        }

        $user->status = $status;

        switch ($status) {
            case 0:
                event(new UserDeactivated($user));
            break;

            case 1:
                event(new UserReactivated($user));
            break;
        }

        if ($user->save()) {
            return true;
        }

        throw new GeneralException(trans('exceptions.backend.access.users.mark_error'));
    }

    /**
     * @param  $input
     * @param  $user
     *
     * @throws GeneralException
     */
    protected function checkUserByEmail($input, $user)
    {
        //Figure out if email is not the same
        if ($user->email != $input['email']) {
            //Check to see if email exists
            if ($this->query()->where('email', '=', $input['email'])->first()) {
                throw new GeneralException(trans('exceptions.backend.access.users.email_error'));
            }
        }
    }

    /**
     * @param $roles
     * @param $user
     */
    protected function flushRoles($roles, $user)
    {
        //Flush roles out, then add array of new ones
        $user->detachRoles($user->roles);
        $user->attachRoles($roles['assignees_roles']);
    }

    /**
     * @param  $roles
     *
     * @throws GeneralException
     */
    protected function checkUserRolesCount($roles)
    {
        //User Updated, Update Roles
        //Validate that there's at least one role chosen
        if (count($roles['assignees_roles']) == 0) {
            throw new GeneralException(trans('exceptions.backend.access.users.role_needed'));
        }
    }

    /**
     * @param  $input
     *
     * @return mixed
     */
    protected function createUserStub($input)
    {
        $user = self::MODEL;
        $user = new $user();
        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->password = bcrypt($input['password']);
        $user->status = isset($input['status']) ? 1 : 0;
        $user->confirmation_code = md5(uniqid(mt_rand(), true));
        $user->confirmed = isset($input['confirmed']) ? 1 : 0;

        return $user;
    }

    public function signup($input)
    {

        $validateRequest = $this->validateApiUser($input);

        if($validateRequest)
        {
            return false;
        }

        $user = new User;
        
        $userData = array(
            'name'      => $input['name'],
            'email'     => $input['email'],
            'username'  => $input['username'],
            'password'  => bcrypt($input['password']),
            'status'    => 1,
            'confirmed' => 1
        );

        $apiUser = $user->create($userData);

        if($apiUser)
        {
            $userMeta = new UserMeta();

            $userMetaData = [
                'user_id'           => $apiUser->id,
                'campus_id'         => isset($input['campus_id']) ? $input['campus_id'] : 1,
                'profile_picture'   => 'default.png'
            ];

            $userMeta->create($userMetaData);
            return $apiUser;
        }

        return false;
    }

    public function validateApiUser($input)
    {
        $user = self::MODEL;
        $user = new $user();
        return $user->where(['email' => $input['email']])->orWhere(['username' => $input['username']])->first();
    }

    /**
     * Forgot Password
     * 
     * @param array $input
     * @return bool
     */
    public function forgotPassword($input)
    {
        if(isset($input['emailid']))
        {
            $user = $this->model->where(['email' => $input['emailid']])->first();

            if($user)
            {   
                $randomPassword = rand(111111, 999999);
                $user->notify(new UserForgotPassword($randomPassword));
                $user->password = bcrypt($randomPassword);
                return $user->save();
            }
        }

        return false;
    }

    /**
     * Get by Id
     *
     * @param int $id
     * @return mixed
     */
    public function getById($id = null)
    {
        if($id)
        {
            return $this->model->with('user_meta')->find($id);
        }
        
        return false;
    } 

    /**
     * Add Interest
     * 
     * @param int $userId
     * @param int $interestId
     * @return bool
     */
    public function addInterest($userId = null, $interestId = null)
    {
        if($userId && $interestId)
        {
            $userInterest = new UserInterest;

            if(strpos($interestId, ',') !== false )
            {
                $interestId = explode(',', $interestId);

                foreach($interestId as $intId)                
                {
                    $interestData[] = [
                        'user_id'       => $userId,
                        'interest_id'   => $intId
                    ];                    

                }
                $userInterest->where(['user_id' => $userId])->delete();                    
                return $userInterest->insert($interestData);                    
            }

            $interestData = [
                'user_id'       => $userId,
                'interest_id'   => $interestId
            ];

            $userInterest->where(['user_id' => $userId])->delete();
            return $userInterest->create($interestData);
        }

        return false;
    }

    /**
     * Add Bulk Interest
     * 
     * @param int $userId
     * @param array|mixed $interests
     */
    public function addBulkInterest($userId = null, $interests)
    {
        if($userId && isset($interests))
        {
            if(is_array($interests))
            {
                $userInterests = $interests;
            }
            else
            {
                $userInterests = explode(',', $interests);    
            }

            $interestInfo = [];

            foreach($userInterests as $interest)
            {
                $interestInfo[] = [
                    'user_id'       => $userId,
                    'interest_id'   => $interest
                ];
            }

            if(count($interestInfo))
            {
                UserInterest::where('user_id', $userId)->delete();
                return UserInterest::insert($interestInfo);
            }
        }

        return false;
    }

    /**
     * Remove Bulk Interest
     * 
     * @param int $userId
     * @param array|mixed $interests
     */
    public function removeBulkInterest($userId = null, $interests)
    {
        if($userId && isset($interests))
        {
            if(is_array($interests))
            {
                $userInterests = $interests;
            }
            else
            {
                $userInterests = explode(',', $interests);    
            }

            return UserInterest::where('user_id', $userId)->whereIn('interest_id', $userInterests)->delete();
        }

        return false;
    }

    /**
     * Get User Interest
     * 
     * @param object $user
     * @return array|mixed
     */
    public function getUserInterest($user = null)
    {
        if($user)
        {
            $userInterests = $user->user_interests->pluck('interest_id')->toArray();
            $userInterests = array_unique($userInterests);

            $interestObj = new Interest;
            return $interestObj->whereIn('id', $userInterests)->select('id as interestId', 'name', 'image')
                ->get()
                ->filter(function($item)
                {
                    if($item->image && file_exists(base_path() . '/public/interests/'.$item->image))
                    {
                        $item->image = url('/interests/'.$item->image);
                    }
                    else
                    {
                        $item->image = url('/interests/default.png');    
                    }        

                    return $item;
                })
                ->toArray();
        }

        return false;
    }

    /**
     * Remove Interest
     * 
     * @param int $userId
     * @param int $interestId
     * @return bool
     */
    public function removeInterest($userId = null, $interestId = null)
    {
        if($userId && $interestId)
        {
            return UserInterest::where(['user_id' => $userId, 'interest_id' => $interestId])->delete();
        }

        return false;        
    }

    public function getAllCampusUsers($user = null)
    {
        $campusId = $user->user_meta->campus_id;

        return $this->model->get()->filter(function($item)  use ($campusId)
        {
            if($item->user_meta && $campusId == $item->user_meta->campus_id)
            {
                return $item;
            }
        });
    }

    /**
     * Set Token
     * 
     * @param object $userInfo
     */
    public function setToken($userInfo = null, $token = null)
    {
        if($userInfo && $token)
        {
            $userToken = new UserToken;

            $userToken->where('user_id', $userInfo->id)->delete();

            UserToken::where('token', $token)->delete();

            $tokenInfo = [
                'user_id'   => $userInfo->id,
                'campus_id' => $userInfo->user_meta->campus_id,
                'token'     => $token
            ];

            return $userToken->create($tokenInfo);
        }

        return false;
    }

    /**
     * Report User
     * 
     * @param object $user
     * @param int $reportUserId
     * @return bool
     */
    public function reportUser($user = null, $reportUserId = null, $notes = 'Report User')
    {
        if($user && $reportUserId)
        {
            return UserReport::create([
                'user_id'           => $user->id,
                'report_user_id'    => $reportUserId,
                'description'       => $notes
            ]);
        }

        return false;
    }
}