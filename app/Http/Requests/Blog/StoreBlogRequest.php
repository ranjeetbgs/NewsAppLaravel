<?php

namespace App\Http\Requests\Blog;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|max:255',
            // 'type' => 'required', 
            // 'description' => 'required', 
            // 'slug' => 'required', 
            // 'source_name' => 'required', 
            // 'source_link' => 'required', 
            // 'voice' => 'required', 
            //'post_id' => 'required', 
            // 'tags' => 'required', 
            // 'short_description' => 'required',
           // 'video_url' => 'required',
          //  'audio_file, social_media_image, created_by,
 //author_name, background_image, status, schedule_date, `order`, is_featured
        ];
    }
}
