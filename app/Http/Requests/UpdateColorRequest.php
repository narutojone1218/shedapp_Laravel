<?php

namespace App\Http\Requests;

use App\Models\Color;
use Store;

use App\Http\Requests\Request;
use App\Validators\ColorValidator;

class UpdateColorRequest extends Request
{
    /**
     * Overwrite laravel's method, define custom validator
     */
    public function validate()
    {
        $this->validator = ColorValidator::make();

        $this->rules();
        $this->runValidator();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // TODO: here should be checking for ownership too

        $colorID = $this->route('color');

        try {
            $color = Color::findOrFail($colorID);
            Store::set('color', $color);

            return true;
        } catch (ModelNotFoundException $e) {
            return false;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $types = implode(',', array_keys(Color::$types));

        $this->validator->addRule('type', "required|string|in:{$types}");
        $this->validator->addRule('name', 'required|string|max:50');
        $this->validator->addRule('hex', 'string|color_hex');
        $this->validator->addRule('url', 'string');
        $this->option_id != 'null' ?? $this->validator->addRule('option_id', 'numeric|exists:options,id,deleted_at,NULL');
        $this->validator->addRule('allowable_models_id', 'array|is_valid_allowable_models');
        return $this->rules;
    }
}
