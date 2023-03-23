@php
use App\Enums\YearsOfExperiences;
use App\Enums\Rio\ConnectionStatusType;
use App\Enums\ServiceSelectionTypes;
use Carbon\Carbon;
@endphp

@extends('layouts.main')

@section('content')
    <div class="container position-relative pb-4 mb-md-3 home pt-6 home--height">
        <div class="row pt-2">
            <div class="col-12">
                <div class="card p-4">
                    @include('rio.profile.components.sections.profile-avatar', ['rio' => $rio])
                    @can('connect', $rio)
                        @if($service->type === ServiceSelectionTypes::RIO)
                            @if (!in_array($status['status'] ?? null, [ConnectionStatusType::HIDDEN, ConnectionStatusType::NOT_ALLOWED]))
                                <rio-connections :rio='@json($rio)' :service='@json($service)' :status='@json($status)' :neo_status='@json($neoStatus)'/>
                            @endif
                        @else
                            @if (!in_array($neoPrivacyStatus, [ConnectionStatusType::HIDDEN, ConnectionStatusType::NOT_ALLOWED]))
                                <rio-connections :rio='@json($rio)' :service='@json($service)' :status='@json($status)' :neo_status='@json($neoPrivacyStatus)'/>
                            @endif
                        @endif
                    @endcan 
                </div>
                <div class="bg-secondary p-4  mt-4 ">
                    {{-- Profile Navigation --}}
                    @include('rio.profile.components.sections.profile-tabs', $rio)
                    
                    <div class="card p-4 shadow">
                        <dl>
                            {{-- Name furigana section --}}
                            @if (!empty($rio->full_name_kana))
                                <dt>{{ __('Furigana Name') }}</dt>
                                <dd>{{ $rio->full_name_kana }}</dd>
                            @endif

                            {{-- Gender section --}}
                            @if (!empty($rio->gender))
                                <dt>{{ __('Gender') }}</dt>
                                <dd>{{ $genders[$rio->gender] }}</dd>
                            @endif

                            {{-- Present Address Prefecture section --}}
                            @if (!empty($rioProfile->present_address_prefecture_formatted))
                                <dt>{{ __('Present Address Prefecture') }}</dt>
                                <dd>{{ $rioProfile->present_address_prefecture_formatted }}</dd>
                            @endif

                            {{-- Present Address City section --}}
                            @if (!empty($rioProfile->present_address_city))
                                <dt>{{ __('Present Address City') }}</dt>
                                <dd>{{ $rioProfile->present_address_city }}</dd>
                            @endif

                            {{-- Home Address Prefecture section --}}
                            @if (!empty($rioProfile->home_address_prefecture_formatted))
                                <dt>{{ __('Home Address Prefecture') }}</dt>
                                <dd>{{ $rioProfile->home_address_prefecture_formatted }}</dd>
                            @endif

                            {{-- Home Address City section --}}
                            @if (!empty($rioProfile->home_address_city))
                                <dt>{{ __('Home Address City') }}</dt>
                                <dd>{{ $rioProfile->home_address_city }}</dd>
                            @endif

                            {{-- Email address section --}}
                            @if (!empty($rio->user->email) && $isProfileOwner)
                                <dt>{{ __('Email Address') }}</dt>
                                <dd>{{ $rio->user->email }}</dd>
                            @endif

                            {{-- Telephone section --}}
                            @if (!empty($rio->tel) && $isProfileOwner)
                                <dt>{{ __('Telephone No.') }}</dt>
                                <dd>{{ $rio->tel }}</dd>
                            @endif

                            {{-- Educational Background section --}}
                            @if ($educationalBackgrounds->isNotEmpty())
                                <dt>{{ __('Educational Background') }}</dt>
                                <dd>
                                    @foreach ($educationalBackgrounds as $background)
                                        <div class="table-responsive bg-gray mt-1">
                                            <table class="table table--firstCompact">
                                                <tr>
                                                    <td>{{ __('School Name') }}:</td>
                                                    <td>{{ $background->content }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('Graduation Date') }}:</td>
                                                    <td>{{ Carbon::parse($background->additional)->format('Y年m日') ?? $background->additional }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    @endforeach
                                </dd>
                            @endif

                            {{-- NEO Affiliation section --}}
                            @if ($neos->isNotEmpty())
                                <dt>{{ __('NEO Affiliation') }}</dt>
                                @foreach ($neos as $neo)
                                    <dd>{{ $neo->organization_name }}</dd>
                                @endforeach
                            @endif

                            {{-- Profession section --}}
                            @if ($professions->isNotEmpty())
                                <dt>{{ __('Profession') }}</dt>
                                @foreach ($professions as $profession)
                                    <dd>{{ $profession->content }}</dd>
                                @endforeach
                            @endif

                            {{-- Qualification section --}}
                            @if ($qualifications->isNotEmpty())
                                <dt>{{ __('Qualification') }}</dt>
                                @foreach ($qualifications as $qualification)
                                    <dd>{{ $qualification->content }}</dd>
                                @endforeach
                            @endif

                            {{-- Acquired Skills section --}}
                            @if ($skills->isNotEmpty())
                                <dt>{{ __('Acquired Skills') }}</dt>
                                @foreach ($skills as $skill)
                                    <dd>{{ $skill->content }}</dd>
                                @endforeach
                            @endif

                            {{-- Award History section --}}
                            @if ($awards->isNotEmpty())
                                <dt>{{ __('Award History') }}</dt>
                                <dd>
                                    @foreach ($awards as $award)
                                        <div class="table-responsive bg-gray mt-1">
                                            <table class="table table--firstCompact">
                                                <tr>
                                                    <td>{{ __('Award Year') }}:</td>
                                                    <td>{{ $award->additional . '年' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('Content') }}:</td>
                                                    <td>{{ $award->content }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    @endforeach
                                </dd>
                            @endif

                            {{-- Industry/Years Of Experience section --}}
                            @if ($industries->isNotEmpty())
                                <dt>{{ __('Industry/Years Of Experience') }}</dt>
                                <dd>
                                    @foreach ($industries as $industry)
                                        <div class="table-responsive bg-gray mt-1">
                                            <table class="table table--firstCompact">
                                                <tr>
                                                    <td>{{ __('Industry') }}:</td>
                                                    <td>{{ $industry->content }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('Years of Experience') }}:</td>
                                                    <td>{{ YearsOfExperiences::getDescription($industry->additional) }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    @endforeach
                                </dd>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include('rio.profile.components.modals.management-menu-modal')
@endsection

@push('vuejs')
    <script src="{{ mix('js/dist/rio/index.js') }}" defer></script>
@endpush