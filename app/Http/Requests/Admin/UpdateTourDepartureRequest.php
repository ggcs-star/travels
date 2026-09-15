<?php

namespace App\Http\Requests\Admin;

class UpdateTourDepartureRequest extends StoreTourDepartureRequest
{
    public function rules(): array
    {
        return parent::rules();
    }
}
