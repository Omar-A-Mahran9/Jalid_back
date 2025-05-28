 @extends('dashboard.partials.master')
 @push('styles')
     <link href="{{ asset('assets/dashboard/css/datatables' . (isDarkMode() ? '.dark' : '') . '.bundle.css') }}"
         rel="stylesheet" type="text/css" />
     <link
         href="{{ asset('assets/dashboard/plugins/custom/datatables/datatables.bundle' . (isArabic() ? '.rtl' : '') . '.css') }}"
         rel="stylesheet" type="text/css" />
 @endpush
 @section('content')
     <!--begin::Basic info-->
     <!--begin::Basic info-->
     <div class="card mb-5 mb-x-10">
         <!--begin::Card header-->

         <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
             data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
             <!--begin::Card title-->
             <div class="card-title m-0">
                 <h3 class="fw-bold m-0">{{ __('Date list') }}</h3>
             </div>
         </div>
         <!--begin::Card header-->

     </div>
     <!--end::Basic info-->

     <div class="card shadow-sm">
         <div class="card-body">
             <form id="crud_form" class="ajax-form" action="{{ route('dashboard.booking_dates.store') }}" method="post"
                 data-success-callback="onAjaxSuccess" data-error-callback="onAjaxError">
                 @csrf

                 <div class="row justify-content-center align-items-start">
                     @foreach (['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                         @php
                             $daySchedule = $schedules[$day] ?? ['is_available' => false, 'times' => []];
                         @endphp

                         <div class="col-md-6 mb-4">
                             <div class="card shadow-sm">
                                 <div class="card-header">
                                     <h5 class="card-title mb-0">{{ __($day) }}</h5>
                                 </div>

                                 <div class="card-body">
                                     {{-- Availability Checkbox with hidden fallback --}}
                                     <input type="hidden" name="schedules[{{ $day }}][is_available]"
                                         value="0" />
                                     <div class="form-check mb-3">

                                         <input class="form-check-input" type="checkbox"
                                             name="schedules[{{ $day }}][is_available]" value="1"
                                             id="{{ $day }}Check"
                                             {{ $daySchedule['is_available'] ? 'checked' : '' }}>

                                         <label class="form-check-label" for="{{ $day }}Check">
                                             {{ __('Available') }}
                                         </label>

                                     </div>

                                     {{-- Time Slots Repeater --}}
                                     <div data-repeater-list="schedules[{{ $day }}][times]">
                                         @forelse ($daySchedule['times'] as $timeItem)
                                             <div data-repeater-item class="d-flex mb-3 align-items-center">
                                                 <input type="time" name="time" class="form-control me-2"
                                                     value="{{ $timeItem['time'] ?? '' }}" />
                                                 <button type="button" data-repeater-delete class="btn btn-sm btn-danger">
                                                     {{ __('Remove') }}
                                                 </button>
                                             </div>
                                         @empty
                                             <div data-repeater-item class="d-flex mb-3 align-items-center">
                                                 <input type="time" name="time" class="form-control me-2" />
                                                 <button type="button" data-repeater-delete class="btn btn-sm btn-danger">
                                                     {{ __('Remove') }}
                                                 </button>
                                             </div>
                                         @endforelse
                                     </div>


                                     <button type="button" data-repeater-create class="btn btn-sm btn-light-primary mt-2">
                                         + {{ __('Add Time Slot') }}
                                     </button>
                                 </div>
                             </div>
                         </div>
                     @endforeach
                 </div>

                 <div class="card-footer text-center">
                     <button type="submit" class="btn btn-primary">
                         <span class="indicator-label">{{ __('Save') }}</span>
                         <span class="indicator-progress" style="display:none;">
                             {{ __('Please wait...') }}
                             <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                         </span>
                     </button>
                 </div>
             </form>
         </div>
     </div>
 @endsection
 @push('scripts')
     <!-- Add jQuery Repeater manually -->

     <script src="{{ asset('assets/dashboard/js/global/datatable-config.js') }}"></script>
     <script src="{{ asset('assets/dashboard/js/datatables/datatables.bundle.js') }}"></script>
     <script src="{{ asset('assets/dashboard/js/datatables/booking_dates.js') }}"></script>
     <script src="{{ asset('assets/dashboard/js/global/crud-operations.js') }}"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js"></script>

     {{-- <script src="{{ asset('assets/dashboard/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script> --}}
     <script src="{{ asset('assets/dashboard/js/components/form_repeater.js') }}"></script>
     <script>
         $(document).ready(function() {
             $("#add_btn").click(function(e) {
                 e.preventDefault();

                 $("#form_title").text(__('Add new date'));
                 $("[name='_method']").remove();
                 $("#crud_form").trigger('reset');
                 $("#crud_form").attr('action', `/dashboard/booking_dates`);
             });


         });
     </script>

     <script>
         $(document).ready(function() {
             $(document).ready(function() {
                 $('[data-repeater-list]').each(function() {
                     $(this).closest('.card-body').repeater({
                         initEmpty: false,
                         defaultValues: {
                             'time': ''
                         },
                         show: function() {
                             $(this).slideDown();
                         },
                         hide: function(deleteElement) {
                             $(this).slideUp(deleteElement);
                         }
                     });
                 });
             });

             $('#add_btn').click(function() {
                 $('#form_title').text('{{ __('Add new date') }}');
                 $("[name='_method']").remove();
                 $('#crud_form').trigger('reset');
                 $('#crud_form').attr('action', '{{ route('dashboard.booking_dates.store') }}');
             });
         });
     </script>

     <script>
         $(document).ready(function() {
             $('[data-repeater-list]').each(function() {
                 $(this).repeater({
                     initEmpty: false,
                     defaultValues: {
                         'time': ''
                     },
                     show: function() {
                         $(this).slideDown();
                     },
                     hide: function(deleteElement) {

                         $(this).slideUp(deleteElement);

                     }
                 });
             });
         });
     </script>
 @endpush
