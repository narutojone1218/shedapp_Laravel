<?php

namespace App\Http\Requests\Locations;

use Store;
use Entrust;
use App\Models\Location;

use App\Http\Requests\Request;
use App\Validators\LocationValidator as Validator;

class UpdateLocationRequest extends Request
{
    /**
     * Overwrite laravel's method, define custom validator
     */
    public function validate()
    {
        $request = $this->all();
        array_walk_recursive($request, function(&$item, $key) {
            if ($item === 'null') $item = null;
        });
        Request::merge($request);
        $this->validator = Validator::make($request)->addRules(Location::$rules);

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

        if (!Entrust::hasRole('administrator')) return false;

        $locationId = $this->route('location');

        try {
            $location = Location::findOrFail($locationId);
            Store::set('requestedLocation', $location);

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
        $this->validator->append('name', 'required');
        $this->validator->append('address', 'required');
        $this->validator->append('city', 'required');
        $this->validator->append('state', 'required');
        $this->validator->append('zip', 'required');
        $this->validator->append('latitude', 'string|is_valid_latitude');
        $this->validator->append('longitude', 'string|is_valid_longitude');

        return $this->rules;
    }
}
