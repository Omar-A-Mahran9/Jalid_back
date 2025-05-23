<!--begin:::Tabs-->
<ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-n2 w-auto">
    <!--begin:::Tab item-->

        <!--begin:::Tab item-->
        <li class="nav-item">
            <a class="nav-link text-active-primary pb-4 {{ getClassIfUrlContains('active', 'banner') }}"
                href="{{ route('dashboard.settings.home.banner') }}">{{ __('banaras') }}</a>
        </li>
        <!--end:::Tab item-->

    <li class="nav-item">
        <a class="nav-link text-active-primary pb-4 {{ getClassIfUrlContains('active', 'about-us') }}"
            href="{{ route('dashboard.settings.home.about-us') }}">{{ __('About us') }}</a>
    </li>
    <!--end:::Tab item-->
    <!--begin:::Tab item-->
    {{-- <li class="nav-item">
        <a class="nav-link text-active-primary pb-4 {{ getClassIfUrlContains('active', 'terms') }}"
            href="{{ route('dashboard.settings.home.terms') }}">{{ __('Terms and conditions') }}</a>
    </li> --}}
    <!--end:::Tab item-->
    <!--begin:::Tab item-->
    <li class="nav-item">
        <a class="nav-link text-active-primary pb-4 {{ getClassIfUrlContains('active', 'privacy-policy') }}"
            href="{{ route('dashboard.settings.home.privacy-policy') }}">{{ __('Privacy Policy') }}</a>
    </li>
    <!--end:::Tab item-->

    <!--begin:::Tab item-->
    <li class="nav-item">
        <a class="nav-link text-active-primary pb-4 {{ getClassIfUrlContains('active', 'our-mission') }}"
            href="{{ route('dashboard.settings.home.our-mission') }}">{{ __('Our Mission') }}</a>
    </li>
    <!--end:::Tab item-->
    <!--begin:::Tab item-->
    <li class="nav-item">
        <a class="nav-link text-active-primary pb-4 {{ getClassIfUrlContains('active', 'our-vission') }}"
            href="{{ route('dashboard.settings.home.our-vission') }}">{{ __('Our Vission') }}</a>
    </li>
    <!--end:::Tab item-->
</ul>
<!--end:::Tabs-->
