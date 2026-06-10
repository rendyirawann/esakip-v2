<?php

namespace common\models;

use Yii;
use yii\base\Model;
use common\models\UserAttemptlogin; // Tambahkan ini

class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    public function login()
{
    $user = $this->getUser();
    $attempt = new UserAttemptlogin();

    $attempt->username = $this->username;
    $attempt->password_hash = Yii::$app->security->generatePasswordHash($this->password);
    $attempt->user_lastlogin = date('Y-m-d H:i:s');
    $attempt->user_lastloginip = Yii::$app->request->userIP;
    $attempt->user_isonline = 'F';  // Default to 'F' for failed attempts
    $attempt->auth_key = Yii::$app->security->generateRandomString(); // Add auth_key value
    $attempt->email = ''; // You can set empty string or null if allowed
    $attempt->created_at = date('Y-m-d H:i:s'); // Set current timestamp for created_at
    $attempt->updated_at = date('Y-m-d H:i:s'); // Set current timestamp for updated_at

    if ($this->validate() && $user) {
        $user->user_lastlogin = date('Y-m-d H:i:s');
        $user->user_lastloginip = Yii::$app->request->userIP;
        $user->user_isonline = 'T';

        // Save successful login attempt
        $attempt->user_isonline = 'T';
        $user->save(false);
        $attempt->save(false);

        return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
    } else {
        // Save failed login attempt
        if ($user) {
            $attempt->nama_user = $user->nama_user;
            $attempt->email = $user->email;
            $attempt->refskpd_id = $user->refskpd_id;
            $attempt->kode_group = $user->kode_group;
        }
        $attempt->save(false);
        return false;
    }
}

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }
        return $this->_user;
    }
}

