<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CitiesResource;
use App\Http\Resources\Api\CommonQuestionResource;
use App\Http\Resources\Api\HowuseResource;
use App\Http\Resources\Api\RateResource;
use App\Http\Resources\Api\ServiceResource;
use App\Http\Resources\Api\SliderResource;

use App\Http\Resources\Api\WhyusResource;

use App\Models\AddonService;
use App\Models\BookingDate;
use App\Models\City;
use App\Models\CommonQuestion;
use App\Models\CustomerRate;
use App\Models\Howuse;
use App\Models\NewsLetter;

use App\Models\Slider;

use App\Models\Whyus;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{





    public function newsLetter(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email:rfc,dns', 'unique:news_letters'],
        ]);

        NewsLetter::create([
            'email' => $request->email
        ]);

        return $this->success(__('Created Successfully'));
    }

    public function getSliders()
    {
        $sliders = Slider::where('status', '1')->get();

        return $this->success('', SliderResource::collection($sliders));
    }


public function getServices()
{
  $locale = app()->getLocale(); // 'ar' or 'en'
    $suffix = $locale === 'ar' ? '_ar' : '_en';

    $services = AddonService::where('is_publish', '1')->get();

    $serviceBannerData = [
        'label'           => setting('label_service' . $suffix),
        'description'     => setting('description_service' . $suffix),
        'service_banner'  => getImagePathFromDirectory(setting('service_banner'), 'Settings'),
    ];

    return $this->success('', [
        'service_banner_data' => $serviceBannerData,

        'services' => ServiceResource::collection($services),
    ]);
}

use Illuminate\Http\Request;
use App\Models\BookingDate;
use Carbon\Carbon;

public function getTime(Request $request)
{
    $request->validate([
        'day_date' => 'required|date',
    ]);

    $bookingDate = BookingDate::with('timeSlots')
        ->where('day_date', $request->day_date)
        ->first();

    if (!$bookingDate || !$bookingDate->is_available) {
        return $this->success('', [
            'time_slots' => [],
        ]);
    }

    $timeSlots = $bookingDate->timeSlots->pluck('time')->mapWithKeys(function ($time) {
        $key = Carbon::createFromFormat('H:i:s', $time)->format('H:i'); // key = "14:30"
        $value = Carbon::createFromFormat('H:i:s', $time)->format('h:i A'); // value = "02:30 PM"
        return [$key => $value];
    });

    return $this->success('', [
        'time_slots' => $timeSlots,
    ]);
}



public function getAvailableDates()
{
    $dates = BookingDate::withCount('timeSlots')
        ->whereDate('day_date', '>=', Carbon::today())
        ->having('time_slots_count', '>', 0)
        ->orderBy('day_date')
        ->pluck('day_date');

    return $this->success('', [
        'available_dates' => $dates->map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        }),
    ]);
}

    public function getwhyus()
    {
        $Whyus = Whyus::get();

        return $this->success('', WhyusResource::collection($Whyus));
    }
    public function getcities()
    {
        $cities = City::get();

        return $this->success('', CitiesResource::collection($cities));
    }
    public function getrates()
        {
            // Fetch all rates from the Rate model
            $rates = CustomerRate::all(); // Or you can use a query like ->where('status', 'approved') to filter rates


            return $this->success('', RateResource::collection($rates));

        }

    public function getQuestions()
    {
        $CommonQuestion = CommonQuestion::get();

        return $this->success('', CommonQuestionResource::collection($CommonQuestion));
    }

    public function getMakeOrder()
    {
        $makeOrder = Howuse::get();

        return $this->success('', HowuseResource::collection($makeOrder));
    }



public function getAboutUs()
{
    $locale = app()->getLocale(); // 'ar' or 'en'
    $suffix = $locale === 'ar' ? '_ar' : '_en';

    $data = [
        'about_us_banner_data' => [
            'label'           => setting('label_about_us' . $suffix),
            'description'     => setting('description_about_us' . $suffix),
            'about_us_banner' => getImagePathFromDirectory(setting('about_us_banner'), 'Settings') ,
        ],
        'about_us_image' => getImagePathFromDirectory(setting('about_us_image'), 'Settings'),
        'about_us'       => setting('about_us' . $suffix),
        'our_mission'    => setting('our_mission' . $suffix),
        'our_vision'     => setting('our_vission' . $suffix), // double-check spelling
    ];

    return $this->success('', $data);
}



public function getprivacypolicy()
{
    $locale = app()->getLocale(); // e.g., 'ar' or 'en'
    $key = 'privacy_policy_' . $locale; // Will resolve to 'privacy_policy_ar' or 'privacy_policy_en'

    $data = setting($key); // Fetch the appropriate setting

    return $this->success('', $data);
}


}
