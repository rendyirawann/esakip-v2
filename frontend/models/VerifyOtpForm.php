<?php
// frontend/models/VerifyOtpForm.php

// frontend/models/VerifyOtpForm.php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

class VerifyOtpForm extends Model
{
    public $otp;
    public $password;

    public function rules()
    {
        return [
            ['otp', 'trim'],
            ['otp', 'required'],
            ['otp', 'string', 'length' => 6], // Sesuaikan panjang OTP sesuai kebutuhan

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
        ];
    }

    public function verifyOtp($userId)
    {
        if ($this->validate()) {
            $user = User::findOne($userId);
            if ($user && $user->status === User::STATUS_INACTIVE) {
                // Verifikasi password
                if ($user->validatePassword($this->password)) {
                    // Aktifkan akun
                    $user->status = User::STATUS_ACTIVE;
                    $user->otp = $this->otp;
                    $user->save();

                    return true;
                }
            }
        }

        return false;
    }
}
