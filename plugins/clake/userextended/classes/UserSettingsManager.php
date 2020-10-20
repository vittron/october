<?php namespace Clake\UserExtended\Classes;

use Clake\Userextended\Models\Field;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

/**
 * User Extended by Shawn Clake
 * Class UserSettingsManager
 * User Extended is licensed under the MIT license.
 *
 * @author Shawn Clake <shawn.clake@gmail.com>
 * @link https://github.com/ShawnClake/UserExtended
 *
 * @license https://github.com/ShawnClake/UserExtended/blob/master/LICENSE MIT
 * @package Clake\UserExtended\Classes
 *
 * @method static UserSettingsManager currentUser() UserSettingsManager
 * @method static UserSettingsManager with($user) UserSettingsManager
 * @method static UserSettingsManager validation() UserSettingsManager
 *
 * Terminology and flow:
 *   A user has many settings.
 *   A setting has many finite options.
 *   Options have default values below.
 *   A setting has a value.
 *   An option has a state.
 */
class UserSettingsManager extends StaticFactory
{

    /**
     * Field types
     * TODO: Change the values to be HTML form types if they aren't already.
     */
    const UE_FORM_TEXT = 'text';
    const UE_FORM_CHECKBOX = 'checkbox';
    const UE_FORM_COLOR = 'color';
    const UE_FORM_DATE = 'date';
    const UE_FORM_EMAIL = 'email';
    const UE_FORM_FILE = 'file';
    const UE_FORM_NUMBER = 'number';
    const UE_FORM_PASSWORD = 'password';
    const UE_FORM_RADIO = 'radio';
    const UE_FORM_RANGE = 'range';
    const UE_FORM_TEL = 'tel';
    const UE_FORM_TIME = 'time';
    const UE_FORM_URL = 'url';
    const UE_FORM_SWITCH = 'switch';

    /**
     * Settings config file
     * @var array
     */
    protected $settingsTemplate = [];

    /**
     * Settings column from the user object
     * @var
     */
    protected $settings;

    /**
     * Stores the user object
     * @var null
     */
    protected $user = null;

    /**
     * Setting option defaults
     * @var array
     */
    private $defaults = [
        'label' => '',
        'type' => 'text',
        'validation' => '',
        'editable' => true,
        'createable' => true,
        'registerable' => false,
        'encrypt' => false,
    ];

    /**
     * Stores the twig environment
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * Factory function for passing in a custom user
     * @param \Clake\Userextended\Models\UserExtended $user
     * @return $this
     */
    public function withFactory(\Clake\Userextended\Models\UserExtended $user)
    {
        $this->user = $user;
        if(isset($this->user))
            $this->settings = $this->user->settings;
        $this->load();
        return $this;
    }

    /**
     * Factory function for using the current user
     * @return $this
     */
    public function currentUserFactory()
    {
        $this->user = UserUtil::convertToUserExtendedUser(UserUtil::getLoggedInUser());
        if(isset($this->user))
            $this->settings = $this->user->settings;
        $this->load();
        return $this;
    }

    /**
     * Loads the settings without getting the current user
     * This is useful for validation while a user isn't logged in.
     * Use cases:
     *   Programatically registering or updating a user
     * @return $this
     */
    public function validationFactory()
    {
        $this->load();
        return $this;
    }

    /**
     * Used as a sort of intermediary function for translating the DB Field Model into something which is backwards compatible
     * TODO: Utilize sort order
     */
    protected function load()
    {
        $loader = new \Twig_Loader_Filesystem(plugins_path('clake/userextended/partials/form'));
        $this->twig = new \Twig_Environment($loader, ['auto_reload' => true]);

        $fields = Field::all();
        $settings = [];

        /** @var Field $field */
        foreach($fields as $field)
        {
            $type = 'self::' . $field->type;

            $settings[$field->code] = [
                'label'        => $field->name,
                'description'  => $field->description,
                'code'         => $field->code,
                'type'         => constant($type),
                'validation'   => $field->validationToString(),
                'data'         => $field->data,
                'editable'     => $field->flags['editable'],
                'registerable' => $field->flags['registerable'],
                'encrypt'      => $field->flags['encrypt'],
                'createable'   => $field->flags['enabled'],
            ];
        }

        $this->settingsTemplate = $settings;
    }

    /**
     * Returns the user settings on the user instance
     * Useful for debugging and tests
     * @return mixed
     */
    public function userSettingsCheck()
    {
        return $this->settings;
    }

    /**
     * Returns the config file contents
     * Useful for debugging and tests
     * @return array
     */
    public function yamlCheck()
    {
        return $this->settingsTemplate;
    }

    /**
     * Returns the user instance
     * Useful for debugging and tests
     * @return null
     */
    public function userCheck()
    {
        return $this->user;
    }

    /**
     * Determines whether the passed string is a valid setting according to the config
     * @param $setting
     * @return bool
     */
    public function isSetting($setting)
    {
        return array_key_exists($setting, $this->settingsTemplate);
    }

    /**
     * Gets the setting's options prioritizing config and then defaults
     * @param $setting
     * @return array|false
     */
    public function getSettingOptions($setting)
    {
        if(!$this->isSetting($setting))
            return false;

        $options = $this->settingsTemplate[$setting];

        return $this->mergeOptionsWithDefaults($options);
    }

    /**
     * Helper function for merging the config options with defaults
     * @param $options
     * @return array
     */
    public function mergeOptionsWithDefaults($options)
    {
        return array_merge($this->defaults, $options);
    }

    /**
     * Gets the value of a setting on a user model
     * @param $setting
     * @return mixed|string
     */
    public function getSettingValue($setting)
    {
        $value = '';

        if(isset($this->settings[$setting]))
            $value = $this->settings[$setting];

        return $value;
    }

    /**
     * Returns an array in the form of [value, options=>[]] for a setting on a user model
     * @param $setting
     * @return array|null
     */
    public function getSetting($setting)
    {
        if(!$this->isSetting($setting))
            return null;

        $value = $this->getSettingValue($setting);

        if(!empty($value))
            $value = $this->decrypt($setting, $value);

        $options = $this->getSettingOptions($setting);

        return [$value, 'options' => $options];
    }

    /**
     * Returns an array in the form of [setting1=>[value. options=>[]], setting2=>[value. options=>[]]]
     * representing all of the settings on a user model
     * @return array
     */
    public function all()
    {
        $settings = [];

        foreach($this->settingsTemplate as $key=>$setting)
        {
            $options = $this->getSettingOptions($key);

            $value = '';

            if(isset($this->settings[$key]))
            {
                $value = $this->settings[$key];
                if($this->isEncrypted($key))
                    $value = $this->decrypt($key, $value);
            }

            $settings[$key] = [$value, 'options' => $options];
        }

        return $settings;
    }

    /**
     * Returns whether or not a setting should exist on an update form page
     * @param $setting
     * @return bool
     */
    public function isEditable($setting)
    {
        $options = $this->getSettingOptions($setting);
        return $options['editable'];
    }

    /**
     * Returns whether or not a setting can be updated or created, Overrides both editable and registerable
     * @param $setting
     * @return mixed
     */
    public function isCreateable($setting)
    {
        $options = $this->getSettingOptions($setting);
        return $options['createable'];
    }

    /**
     * Returns whether a setting should exist on a sign up form
     * @param $setting
     * @return mixed
     */
    public function isRegisterable($setting)
    {
        $options = $this->getSettingOptions($setting);
        return $options['registerable'];
    }

    /**
     * Returns whether or not a setting has validation rules
     * @param $setting
     * @return bool
     */
    public function isValidated($setting)
    {
        $options = $this->getSettingOptions($setting);
        return $options['validation'] != '' && isset($options['validation']);
    }

    /**
     * Returns whether or not a setting should be encrypted
     * @param $setting
     * @return bool
     */
    public function isEncrypted($setting)
    {
        $options = $this->getSettingOptions($setting);
        return $options['encrypt'];
    }

    /**
     * Returns whether or not a passed value passes its validation rules
     * Will return true if the setting does not require validation
     * @param $setting
     * @param $value
     * @return bool|Validator\
     */
    public function validate($setting, $value)
    {
        $options = $this->getSettingOptions($setting);

        if($this->isValidated($setting))
        {
            $validator = Validator::make(
                [$options['label'] => $value],
                [$options['label'] => $options['validation']]
            );

            if($validator->fails())
                return $validator;
        }

        return true;
    }

    /**
     * Returns an encrypted version of the passed value.
     * It will return the NON encrypted value if encryption is not required for the setting
     * @param $setting
     * @param $value
     * @return mixed
     */
    public function encrypt($setting, $value)
    {
        if($this->isEncrypted($setting))
        {
            $value = Crypt::encrypt($value);
        }

        return $value;
    }

    /**
     * Returns the decrypted version of the passed value
     * It will return the value if encryption is not required
     * @param $setting
     * @param $value
     * @return mixed
     */
    public function decrypt($setting, $value)
    {
        if($this->isEncrypted($setting))
        {
            $value = Crypt::decrypt($value);
        }

        return $value;
    }

    /**
     * Sets a setting by checking whether or not it can be edited, then validates it, then encrypts it if requried.
     * @param $setting
     * @param $value
     * @return bool|Validator
     */
    public function setSetting($setting, $value)
    {
        $validator = $this->validate($setting, $value);

        if($validator !== true)
            return $validator;

        $value = $this->encrypt($setting, $value);

        if($this->settings == "Array" || is_null($this->settings) || empty($this->settings))
            $this->settings = [];

        $this->settings[$setting] = $value;

        return true;
    }

    /**
     * Save the settings to the user model
     * @return $this
     */
    public function save()
    {
        \Clake\Userextended\Models\UserExtended::where('id', $this->user->id)->update(['settings'=>json_encode($this->settings)]);
        return $this;
    }

    /**
     * Returns an array of setting values and options for each setting marked with the option 'editable'
     * @return array
     */
    public function getUpdateable()
    {
        $settings = [];

        foreach($this->getSettingsTemplate() as $key=>$setting)
        {
            if(!$this->isCreateable($key))
                continue;

            if(!$this->isEditable($key))
                continue;

            $options = $this->getSettingOptions($key);

            $value = '';

            if(isset($this->settings[$key]))
            {
                $value = $this->settings[$key];
                if($this->isEncrypted($key))
                    $value = $this->decrypt($key, $value);
            }

            $settings[$key] = [$value, 'options' => $options, 'html' => $this->render($key, $options)];

        }

        return $settings;
    }

    /**
     * Returns an array of setting values and options for each setting marked with the option 'registerable'
     * @return array
     */
    public function getRegisterable()
    {
        $settings = [];

        foreach($this->settingsTemplate as $key=>$setting)
        {
            if(!$this->isCreateable($key))
                continue;

            if(!$this->isRegisterable($key))
                continue;

            $options = $this->getSettingOptions($key);

            $settings[$key] = ['options' => $options, 'html' => $this->render($key, $options)];
        }

        return $settings;
    }

    /**
     * @param $settingName
     * @param $options
     * @return mixed
     */
    public function render($settingName, $options)
    {
        /*
         * Don't render core fields EVER from a users perspective.
         */
        if(isset($options['data']['core']) && $options['data']['core'] == true)
        {
            return '';
        }

        $class = '';
        if(isset($options['data']['class']))
        {
            $class = $options['data']['class'];
            unset($options['data']['class']);
        }

        return $this->twig->render('input_' . $options['type'] . '.htm', [
            'type'  => $options['type'],
            'label' => $options['label'],
            'id'    => 'accountSettings' . $settingName,
            'name'  => $settingName,
            'data'  => $options['data'],
            'class' => $class,
            'value' => $this->getSetting($settingName)[0],
        ]);
    }

    /**
     * Get templated settings
     * @param bool $core
     * @return array
     */
    public function getSettingsTemplate($core = false)
    {
        if(!$core)
        {
            $settings = [];
            foreach($this->settingsTemplate as $key => $setting)
            {
                if(!(isset($setting['data']['core']) && $setting['data']['core']))
                {
                    $settings[$key] = $setting;
                }
            }
        } else {
            $settings = $this->settingsTemplate;
        }

        return $settings;
    }

    /**
     * @param $setting
     * @param $value
     * @return bool|Validator\
     */
    public function checkValidation($setting, $value)
    {
        if(!$this->isSetting($setting))
            return false;

        $validator = $this->validate($setting, $value);

        if($validator !== true)
            return $validator;

        return true;
    }
}
