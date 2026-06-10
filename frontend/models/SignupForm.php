<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;


/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $no_hp;
    public $otp;
    public $user;
    public $id;



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],

            ['no_hp', 'trim'],
            ['no_hp', 'required'],
            ['no_hp', 'match', 'pattern' => '/^(08|628)\d{9,}$/', 'message' => 'Nomor HP harus dimulai dengan "08" atau "628" dan memiliki panjang minimal 11 digit.'],
            ['no_hp', 'validateNoHpUnique'], // Tambahkan aturan validasi khusus untuk mengecek duplikasi

            ['otp', 'trim'],
            ['otp', 'string', 'length' => 6], // Sesuaikan panjang OTP sesuai kebutuhan
            ['otp', 'validateOtp'],
        ];
    }

    public function getUser()
    {
        return $this->user;
    }

    public function validateOtp($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = User::findByNoHp($this->normalizePhoneNumber($this->no_hp));

            if (!$user || !$user->validateOtp($this->otp) || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Kode OTP atau password salah.');
            }
        }
    }


    public function validateNoHpUnique($attribute, $params)
    {
        $normalizedNoHp = $this->normalizePhoneNumber($this->no_hp);

        $existingUser = User::findOne(['no_hp' => $normalizedNoHp]);
        if ($existingUser) {
            $this->addError($attribute, 'Nomor HP sudah terdaftar. Gunakan nomor HP lain.');
        }
    }

    private function normalizePhoneNumber($phoneNumber)
    {
        // Hanya manipulasi jika awalan adalah "08"
        if (strpos($phoneNumber, '08') === 0) {
            return '628' . substr($phoneNumber, 2);
        }

        return $phoneNumber;
    }

    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->no_hp = $this->normalizePhoneNumber($this->no_hp);
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->generateOtp();  // Pastikan ini dipanggil sebelum menyimpan user
            $user->status = User::STATUS_INACTIVE; // Set status to inactive (0)
            $user->created_at = time();
            $user->updated_at = time();

            if ($user->save()) {
                // Send OTP via WhatsApp API
                $this->sendOtpViaWhatsapp($user->no_hp, $user->otp, $user->id);


                Yii::$app->session->setFlash('success', 'Berhasil membuat akun, silahkan verifikasi OTP.');
                return true;
            }
        }

        return false;
    }


    private function sendOtpViaWhatsapp($no_hp, $otp, $userId)
    {
        $verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-otp', 'id' => $userId]);

        $messageTemplate = "Salam sejahtera,\n\nkami memberikan Anda Kode One-Time Password (OTP) yang diperlukan untuk mengaktifkan akun anda.\n\nKode OTP: $otp\n\nMohon pastikan Anda memasukkan OTP ini dengan cepat dan aman pada platform yang ditentukan untuk memvalidasi dan mengotorisasi tindakan yang dimaksud. Jangan bagikan OTP ini kepada siapapun.\n\nAnda juga bisa verifikasi melalui link berikut:\n$verifyLink\n\nSalam hormat,\n\nAdmin Aplikasi Dokrenbang";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.saungwa.com/api/create-message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'appkey' => '3b0d0965-67c3-48e3-9ecd-b8c29a28b9dc',
                'authkey' => 'yBzR9LniWynYt6hMm5fuBfOOu3bv1KrclWpwaPSeWsANIho7eq',
                'to' => $no_hp,
                'message' => $messageTemplate,
                'sandbox' => 'false'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }







    // public function signup()
    //     {
    //         if ($this->validate()) {
    //             $user = new User();
    //             $user->username = $this->username;
    //             $user->email = $this->email;
    //             $user->setPassword($this->password);
    //             $user->generateAuthKey();
    //             // $user->generateEmailVerificationToken();
    //             $user->status = User::STATUS_ACTIVE; // Set status to active (10)
    //             $user->bidang_id = 0; // Set bidang_id to 0
    //             $user->created_at = time(); // Set created_at to current time
    //             $user->updated_at = time(); // Set updated_at to current time

    //             // if ($user->save() && $this->sendEmail($user)) {
    //             //     return true;
    //             // }
    //             if ($user->save()) {
    //                 return true;
    //             }
    //         }

    //         return false;
    //     }


    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
    // protected function sendEmail($user)
    // {
    //     return Yii::$app
    //         ->mailer
    //         ->compose()
    //         ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
    //         ->setTo($this->email)
    //         ->setSubject('Account registration at ' . Yii::$app->name)
    //         ->setHtmlBody('<b>Your HTML content goes here</b>')
    //         ->setTextBody('Your plain text content goes here')
    //         ->send();
    // }

}
