<?php

namespace Aviator\Helpdesk\Requests\Admin\Teams;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules (): array
    {
        return [
            'name' => [
                'required',
            ],
        ];
    }
}
