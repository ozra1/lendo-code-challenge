<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'required|int',
            'items' => 'required|array',
            'items.*.shop_id' => 'required|exists:shop,id',
            'items.*.quantity' => 'required|int',
            'items.*.price' => 'required|numeric',
            'items.*.month_count' => 'required|in:3,6,9,12',
        ];
    }
}
