<?php

namespace frontend\models;

use common\models\User;
use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

/**
 * Форма сброса пароля
 */
class ResetPasswordForm extends Model
{
    public $password;

    /**
     * @var User
     */
    private $_user;

    /**
     * Создает форму для сброса пароля по токену.
     *
     * @param string $token
     * @param array $config пары имя-значение, которые будут использоваться для инициализации свойств объекта
     * @throws InvalidParamException если токен пустой или не действителен
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Токен сброса пароля не может быть пустым.');
        }
        $this->_user = User::findByPasswordResetToken($token);
        if (!$this->_user) {
            throw new InvalidParamException('Неверный токен сброса пароля.');
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Сброс пароля.
     *
     * @return boolean если пароль был  сброшен.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->password = $this->password;
        $user->removePasswordResetToken();
        $user->generateAuthKey();

        return $user->save();
    }
}
