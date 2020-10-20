<?php namespace Clake\UserExtended\Classes;

use Clake\Userextended\Models\Field;
use Illuminate\Support\Facades\Validator;

/**
 * User Extended by Shawn Clake
 * Class FieldManager
 * User Extended is licensed under the MIT license.
 *
 * @author Shawn Clake <shawn.clake@gmail.com>
 * @link https://github.com/ShawnClake/UserExtended
 *
 * @license https://github.com/ShawnClake/UserExtended/blob/master/LICENSE MIT
 * @method static FieldManager all() FieldManager
 * @package Clake\UserExtended\Classes
 */
class FieldManager extends StaticFactory
{

    /**
     * Contains all of the currently existing fields
     * @var
     */
    private $fields;

    /**
     * Creates a field
     * @param $name
     * @param $code
     * @param $description
     * @param string $validation
     * @param string $type
     * @param array $flags
     * @param array $data
     * @return Field
     */
	public static function createField($name,
                                       $code,
                                       $description,
                                       $validation = "",
                                       $type = UserSettingsManager::UE_FORM_TEXT,
                                       $flags = [],
                                       $data = []
    ) {

        if(empty($code))
            $code = $name;

        $code = str_slug($code, "-");

        $validator = Validator::make(
            [
                'name' => $name,
                'description' => $description,
                'code' => $code,
            ],
            [
                'name' => 'required|min:3',
                'description' => 'required|min:8',
                'code' => 'required',
            ]
        );

        if($validator->fails())
        {
            return $validator;
        }

        // Duplicate code
        if(Field::where('code', $code)->count() > 0)
            return false;

		$field = new Field();
		$field->name = $name;
		$field->code = str_slug($code, "-");
		$field->description = $description;
		$field->type = $type;
		$field->validation = $validation;
		if(!empty($flags)) $field->flags = $flags;
        if(!empty($data)) $field->data = $data;

		$field->save();
		return $validator;
	}

    /**
     * Generates a flags array. This is a helper function
     * @param bool $enabled
     * @param bool $registerable
     * @param bool $editable
     * @param bool $encryptable
     * @return array
     */
	public static function makeFlags($enabled = false, $registerable = true, $editable = true, $encryptable = false)
    {
        return [
            'enabled' => $enabled,
            'registerable' => $registerable,
            'editable' => $editable,
            'encrypt' => $encryptable
        ];
    }

    /**
     * Updates a field.
     * @param $name
     * @param $code
     * @param $description
     * @param string $type
     * @param string $validation
     * @param array $flags
     * @param array $data
     * @return array
     */
	public static function updateField($name,
                                       $code,
                                       $description,
                                       $type = UserSettingsManager::UE_FORM_TEXT,
                                       $validation = "",
                                       $flags = [],
                                       $data = []
    ) {

	    $field = FieldManager::findField($code);
		$field->name = $name;
		$field->code = str_slug($code, "-");
		$field->description = $description;
		$field->type = $type;
		$field->validation = $validation;
		if(!empty($flags)) $field->flags = $flags;
        if(!empty($data)) $field->data = $data;

        $validator = Validator::make(
            [
                'name' => $field->name,
                'description' => $field->description,
                'code' => $field->code,
            ],
            [
                'name' => 'required|min:3',
                'description' => 'required|min:8',
                'code' => 'required',
            ]
        );

        if($validator->fails())
        {
            return $validator;
        }

        if(Field::code($field->code)->where('id', '<>', $field->id)->count() > 0)
            return false;

		$field->save();
		return $validator;
	}

    /**
     * Deletes a field
     * @param $code
     */
	public static function deleteField($code)
	{
		$field = FieldManager::findField($code);
		$field->delete();
	}

    /**
     * Factory function which fills the field manager with all existing fields
     * @return $this
     */
	public function allFactory()
    {
        $this->fields = Field::all();
        return $this;
    }
	
	/**
	 * Finds and returns a Field and its properties
	 * @param $code
	 * @return array
	 */
	public static function findField($code)
	{
		return Field::where('code', $code)->first();
	}
	
	/**
     * TODO: I'm not sure this function is needed.
     *
	 * Determines if the given field is required to register
	 * @param $code
	 * @return boolean
	 */
    public static function isRequried($code)
    {
        $field = Field::where('code', $code)->first();
        return $field->flags['registerable'];
    }

    /**
     * Returns all of the custom fields in order
     * @return array
     */
    public function getSortedFields()
    {
        $fields = [];

        if(empty($this->fields))
            return [];

        foreach($this->fields as $field)
        {
            $fields[$field["sort_order"]] = $field;
        }

        ksort($fields);

        return $fields;
    }
}