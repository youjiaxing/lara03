<?php
/**
 *
 * @author : 尤嘉兴
 * @version: 2020/8/6 15:53
 */

namespace App\Http\Requests\Api;

class TopicRequest extends \App\Http\Requests\Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'title' => ['string', 'between:2, 200'],
            'body' => ['string', 'min:10'],
            'category_id' => ['numeric', 'exists:categories,id'],
        ];

        switch ($this->method) {
            case 'POST':
                array_unshift($rules['title'], 'required');
                array_unshift($rules['body'], 'required');
                array_unshift($rules['category_id'], 'required');
                break;
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'title' => '标题',
            'body' => '内容',
            'category_id' => '分类',
        ];
    }


}