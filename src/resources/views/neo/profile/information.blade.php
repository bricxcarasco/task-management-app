@php
use App\Enums\YearsOfExperiences;
use App\Enums\Neo\OrganizationAttributeType;
use App\Enums\Neo\RoleType;
use App\Enums\Neo\NeoBelongStatusType;
use App\Enums\Neo\AcceptParticipationType;
use App\Enums\Neo\RestrictConnectionType;
use App\Enums\ServiceSelectionTypes;
use App\Enums\Neo\ConnectionStatusType;
use Carbon\Carbon;

if(!empty($neo->business_hours)) {
    $business_hours = $neo->business_hours;
}
@endphp

@extends('layouts.main')

@section('content')

    @include('layouts.components.neo.management_menu_modal')
    @include('layouts.components.neo.profile_apply_modal')
    @include('layouts.components.neo.profile_cancel_participation_modal', ['neoId' => $neo->id])
    @include('layouts.components.neo.profile_dialogue_modal')
    @include('layouts.components.neo.exit_neo_modal')
    @include('layouts.components.neo.profile_apply_connection_modal')
    @include('layouts.components.neo.profile_connection_dialogue_modal')
    @include('layouts.components.neo.profile_cancel_connection_modal')
    @include('layouts.components.neo.profile_disconnect_connection_modal')
    <div class="container position-relative pb-4 mb-md-3 home pt-6 home--height">

        {{-- Flash Alert --}}
        @include('components.alert')

        <div class="row">
            <div class="col-12">
                <div class="card p-4">
                    @include('neo.profile.components.profile-avatar')
                    <div class="d-flex" id="introduction-buttons">
                        @if(!$neoBelong || ($serviceSelected->type === ServiceSelectionTypes::NEO && $serviceSelected->data->id !== $neo->id))
                            @if(!$isParticipant && $serviceSelected && $serviceSelected->type !== ServiceSelectionTypes::NEO && $privacySetting->accept_belongs === AcceptParticipationType::ACCEPT_PARTICIPATION)
                                <button class="fs-xs btn btn-primary mt-2 me-2" id="participate-btn" data-toggle="modal" type="button" data-bs-toggle="modal" data-bs-target="#apply" style="width: 200px;">{{ __('Application for participation') }}</button>
                            @endif
                        @elseif($neoBelong && $isParticipant && $serviceSelected && $serviceSelected->type !== ServiceSelectionTypes::NEO && $privacySetting->accept_belongs === AcceptParticipationType::ACCEPT_PARTICIPATION && ($neoBelong->status ?? null) === NeoBelongStatusType::APPLYING)
                            <button class="fs-xs btn btn-primary mt-2 me-2" id="cancel-participate-btn" data-toggle="modal" type="button" data-bs-toggle="modal" data-bs-target="#cancel-participation">{{ __('Cancellation of participation application') }}</button>
                        @elseif((!in_array(($neoBelong->role ?? null), [RoleType::OWNER])) && (($neoBelong->status ?? null) === NeoBelongStatusType::AFFILIATE))
                            <button class="fs-xs btn btn-primary mt-2 me-2" id="exitNeoBTN" data-toggle="modal" type="button" data-bs-toggle="modal" data-bs-target="#exit-modal" style="width: 200px;">{{ __('Exit from this NEO') }}</button>
                        @endif
                        @if(($serviceSelected->type === ServiceSelectionTypes::NEO && $serviceSelected->data->id !== $neo->id) ||
                            ($serviceSelected->type === ServiceSelectionTypes::RIO &&
                                ($neo->id !== $user->rio->id || empty($serviceSelected->data->organization_type)) &&
                                (!in_array(($neoBelong->role ?? null), [RoleType::OWNER]))))
                            @if(!$neoRequest && !$neoConnection && $privacySetting->accept_connections === RestrictConnectionType::ACCEPT_CONNECTION)
                                <button class="fs-xs btn btn-primary mt-2 me-2" id="connection-btn" data-toggle="modal" type="button" data-bs-toggle="modal" data-bs-target="#apply-connection" style="width: 200px;">{{ __('Connection application') }}</button>
                            @endif
                            @if(!$neoRequest && $neoConnection && $neoConnection->status === ConnectionStatusType::APPLYING)
                                <button class="fs-xs btn btn-primary mt-2 me-2" id="cancel-connection-btn" data-toggle="modal" type="button" data-bs-toggle="modal" data-bs-target="#cancel-connection">{{ __('Cancellation of connection application') }}</button>
                            @endif
                            @if(($neoConnection && $neoConnection->status === ConnectionStatusType::CONNECTION_STATUS) ||
                                (($neoRequest && $neoRequest->status === ConnectionStatusType::CONNECTION_STATUS)))
                                <button class="fs-xs btn btn-primary mt-2 me-2" id="disconnect-connection-btn" data-toggle="modal" type="button" data-bs-toggle="modal" data-bs-target="#disconnect-connection" style="width: 200px;">{{ __('Disconnection') }}</button>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="mt-4">
                    @include('layouts.components.neo.profile_tab_links', ['active' => __('Information')])

                    <div class="card p-4 shadow">
                        <dl>
                            {{-- Organization Name furigana section --}}
                            @if (!empty($neo->organization_kana))
                                <dt>{{ __('Organization name furigana') }}</dt>
                                <dd>{{ $neo->organization_kana }}</dd>
                            @endif

                            {{-- Organization Type section --}}
                            @if (!empty($neo->organization_type))
                                <dt>{{ __('Organizational attributes') }}</dt>
                                <dd>{{ OrganizationAttributeType::getDescription($neo->organization_type) }}</dd>
                            @endif

                            {{-- Prefecture section --}}
                            @if (!empty($neoProfile->address_prefecture_formatted))
                                <dt>{{ __('Present Address Prefecture') }}</dt>
                                <dd>{{ $neoProfile->address_prefecture_formatted }}</dd>
                            @endif

                            {{-- City section --}}
                            @if (!empty($neoProfile->city))
                                <dt>{{ __('City, Town, Village') }}</dt>
                                <dd>{{ $neoProfile->city }}</dd>
                            @endif

                            {{-- Address section --}}
                            @if (!empty($neoProfile->address))
                                <dt>{{ __('Address after that') }}</dt>
                                <dd>{{ $neoProfile->address }}</dd>
                            @endif

                            {{-- Date of Establishment section --}}
                            @if (!empty($neo->establishment_date))
                                <dt>{{ __('Date of Establishment') }}</dt>
                                <dd>{{ Carbon::parse($neo->establishment_date)->format('Y年m日') ?? $neo->establishment_date }}</dd>
                            @endif

                            {{-- Business History --}}
                            @if (!empty($neo->business_duration))
                                <dt>{{ __('Business History') }}</dt>
                                <dd>{{ $neo->business_duration }}</dd>
                            @endif

                            {{-- Email Address section --}}
                            @if ($emails->isNotEmpty())
                                <dt>{{ __('Email Address') }}</dt>
                                @foreach ($emails as $email)
                                    <dd>{{ $email->content }}</dd>
                                @endforeach
                            @endif

                            {{-- Telephone section --}}
                            @if (!empty($neo->tel))
                                <dt>{{ __('Telephone No.') }}</dt>
                                <dd>{{ $neo->tel }}</dd>
                            @endif

                            {{-- Urls section --}}
                            @if ($urls->isNotEmpty())
                                <dt>{{ __('Various URLs') }}</dt>
                                <dd>
                                    @foreach ($urls as $url)
                                        <div class="table-responsive bg-gray mt-1">
                                            <table class="table table--firstCompact">
                                                <tr>
                                                    <td>{{ __('Title') }}:</td>
                                                    <td>{{ $url->content }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('URL') }}:</td>
                                                    <td>{{ $url->information }}
                                                    </td>
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

                            {{-- History section --}}
                            @if ($histories->isNotEmpty())
                                <dt>{{ __('History') }}</dt>
                                <dd>
                                    @foreach ($histories as $history)
                                        <div class="table-responsive bg-gray mt-1">
                                            <table class="table table--firstCompact">
                                                <tr>
                                                    <td>{{ __('Year/Month') }}:</td>
                                                    <td>{{ Carbon::parse($history->additional)->format('Y年m日') ?? $history->additional }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('Content') }}:</td>
                                                    <td>{{ $history->content }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    @endforeach
                                </dd>
                            @endif

                            {{-- Business Hours section --}}
                            @if (!empty($business_hours['content']))
                                <dt>{{ __('Business Hours') }}</dt>
                                <dd>{{ $business_hours['content'] }}</dd>
                            @endif

                            {{-- Holiday section --}}
                            @if (!empty($business_hours['additional']))
                                <dt>{{ __('Holiday') }}</dt>
                                <dd>{{ $business_hours['additional'] }}</dd>
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

                            {{-- Overseas Support section --}}
                                <dt>{{ __('Overseas Support') }}</dt>
                                <dd>{{ overseas_value($overseasCorrespondence) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
