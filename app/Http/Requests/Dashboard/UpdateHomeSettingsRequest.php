<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomeSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return abilities()->contains('update_settings');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        $validationKey = request()->segment(count(request()->segments()));

        $validations = [
            "about-us" => [
                'label_ar' => 'required|string',
                'label_en' => 'required|string',
                'about_us_ar' => 'required|string',
                'about_us_en' => 'required|string',
                'about_us_image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:512',

            ],
            "banner" => [
                'label_about_us_ar' => 'required|string',
                'label_about_us_en' => 'required|string',
                'description_about_us_ar' => 'required|string',
                'description_about_us_en' => 'required|string',

                'label_service_ar' => 'required|string',
                'label_service_en' => 'required|string',
                'description_service_ar' => 'required|string',
                'description_service_en' => 'required|string',

                'label_contact_ar' => 'required|string',
                'label_contact_en' => 'required|string',
                'description_contact_ar' => 'required|string',
                'description_contact_en' => 'required|string',


                'label_about_us_ar' => 'required|string',
                'label_about_us_en' => 'required|string',
                'description_about_us_ar' => 'required|string',
                'description_about_us_en' => 'required|string',


                'about_us_banner' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:512',
                'service_banner' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:512',
                'contact_banner' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:512',

            ],
            'main' => [
                'home_page.why_choose_us_main_description_ar' => 'required|string|max:600',
                'home_page.why_choose_us_main_description_en' => 'required|string|max:600',
                'home_page.why_choose_us_title_1_ar' => 'required|string|max:255',
                'home_page.why_choose_us_title_1_en' => 'required|string|max:255',
                'home_page.why_choose_us_description_1_ar' => 'required|string|max:600',
                'home_page.why_choose_us_description_1_en' => 'required|string|max:600',
                'home_page.why_choose_us_title_2_ar' => 'required|string|max:255',
                'home_page.why_choose_us_title_2_en' => 'required|string|max:255',
                'home_page.why_choose_us_description_2_ar' => 'required|string|max:600',
                'home_page.why_choose_us_description_2_en' => 'required|string|max:600',
                'home_page.why_choose_us_title_3_ar' => 'required|string|max:255',
                'home_page.why_choose_us_title_3_en' => 'required|string|max:255',
                'home_page.why_choose_us_description_3_ar' => 'required|string|max:600',
                'home_page.why_choose_us_description_3_en' => 'required|string|max:600',
            ],
            "terms" => [
                'terms_ar' => 'required|string',
                'terms_en' => 'required|string',
            ],
            "privacy-policy" => [
                'privacy_policy_ar' => 'required|string',
                'privacy_policy_en' => 'required|string',
            ],
            "return-policy" => [
                'return_policy_ar' => 'required|string',
                'return_policy_en' => 'required|string',
            ],
            "our-mission" => [
                'our_mission_ar' => 'required|string',
                'our_mission_en' => 'required|string',
            ],
            "our-vission" => [
                'our_vission_ar' => 'required|string',
                'our_vission_en' => 'required|string',
            ]
        ];

        return request()->isMethod('post') ? $validations[$validationKey] : [];
    }
}
