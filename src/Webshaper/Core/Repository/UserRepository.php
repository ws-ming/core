<?php namespace Webshaper\Core\Repository;

use Webshaper\Core\Models\User as User;
use \Webshaper\Core\UserInterface as UserInterface;
class UserRepository extends BaseRepository implements UserInterface
{

    function __construct(User $model){

        parent::__construct($model);
    }

    /**
     * @param $loginID user login id
     * @param $password plain password
     * @return bool $status
     */
    public function authenticate($loginID,$password,$type='API'){

        $user = $this->model->where('txtLoginID',$loginID)->where('txtPwd',$password)->take(1)->get();
        if(count($user) === 0 || $user[0] === null) return false;
        //record the activity log if success login
        \LogRepo::insertLog($user[0]->txtLoginID,'login',$type);
        return true;
    }


}