<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "user_attemptlogin".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property string|null $nama_user
 * @property int|null $refskpd_id
 * @property int|null $kode_group
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string|null $user_lastlogin
 * @property string|null $user_lastloginip
 * @property string|null $user_isonline
 */

class UserAttemptlogin extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_attemptlogin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at'], 'required'],
            [['nama_user', 'user_isonline'], 'string'],
            [['refskpd_id', 'kode_group', 'status', 'created_at', 'updated_at'], 'integer'],
            [['user_lastlogin'], 'safe'],
            [['username', 'auth_key'], 'string', 'max' => 32],
            [['password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['user_lastloginip'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'nama_user' => 'Nama User',
            'refskpd_id' => 'Refskpd ID',
            'kode_group' => 'Kode Group',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'user_lastlogin' => 'User Lastlogin',
            'user_lastloginip' => 'User Lastloginip',
            'user_isonline' => 'User Isonline',
        ];
    }
}
