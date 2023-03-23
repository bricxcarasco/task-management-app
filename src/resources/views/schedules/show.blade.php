@extends('layouts.main')

@section('content')
<div class="container position-relative zindex-5 pt-6 py-md-6 mb-md-3 home--height" >
        {{-- Alert --}}
        @include('components.alert')
        <div class="row">
          <!-- Content-->
          <div class="col-12 col-md-9 offset-md-3">
            <div class="d-flex align-items-center justify-content-between  mb-0 mb-md-2 position-relative border-bottom pb-2">
            <a href="{{ route('schedules.index') }}">
              <button type="button" class="btn btn--link">
                  <i class="ai-arrow-left"></i>
                </button>
              </a>
              <div class="text-end">
              <form method="POST" action="{{ route('schedule.accept-participation', $schedule->id) }}" class="d-inline" novalidate>
                  @csrf
                  {{ method_field('PATCH') }}
                  <button type="submit" class="btn btn-primary">{{ __('Participation') }}</button>
              </form>
              <form method="POST" action="{{ route('schedule.decline-participation', $schedule->id) }}" class="d-inline" novalidate>
                  @csrf
                  {{ method_field('PATCH') }}
                  <button type="submit" class="btn btn-link">{{ __('Non-participation') }}</button>
              </form>
              </div>
            </div>
            <div class="mt-2">
                {{-- <div class="text-end">
                  <a href="#" class="btn btn-link">{{ __('Register with google calender') }}</a>
                </div> --}}
                <div class="mb-3">
                    <label for="normal-input" class="form-label">■{{ __('Title') }}<sup class="text-danger ms-1">*</sup></label>
                    <p class="ps-3">{{ $schedule->schedule_title }}</p>
                </div>
                <div class="mb-3">
                    <label for="normal-input" class="form-label">■{{ __('Date and time') }}<sup class="text-danger ms-1">*</sup></label>
                    <div class="row">
                        <div class="col-6 d-flex align-items-center justify-content-between pe-0">
                            <label for="text-input" class="form-label ms-0 me-2">{{ __('Start') }}</label>
                            <p class="m-0">{{ $schedule->start_date }}</p>
                        </div>
                        <div class="col-6 d-flex align-items-center justify-content-between ps-0">
                            <label for="text-input" class="form-label ms-0 me-2">{{ __('Time of Day') }}</label>
                            <p class="m-0">{{ $schedule->start_time }}</p>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6 d-flex align-items-center justify-content-between pe-0">
                            <label for="text-input" class="form-label ms-0 me-2">{{ __('End') }}</label>
                            <p class="m-0">{{ $schedule->end_date }}</p>
                        </div>
                        <div class="col-6 d-flex align-items-center justify-content-between ps-0">
                            <label for="text-input" class="form-label ms-0 me-2">{{ __('Time of Day') }}</label>
                            <p class="m-0">{{ $schedule->end_time }}</p>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                  <p class="mb-0 pe-2">■{{ __('Participant') }}</p>
                </div>
                <div class="card p-4 shadow mt-2 mb-3">
                  <div class="connection__wrapper">
                    <ul class="connection__lists list-group list-group-flush mt-2">
                        @foreach ($schedule->guests as $guests)
                        <li class="d-flex justify-content-between align-items-center list-group-item px-0 py-2 position-relative list--white px-2">
                        <div>
                            <a href="{{ $guests->rio ? route('rio.profile.introduction', $guests->rio_id) : route('neo.profile.introduction', $guests->neo_id)  }}">
                              <img class="rounded-circle me-2 d-inline-block img--profile_image_sm" src="{{ $guests->rio->rio_profile->profile_image ?? $guests->neo->neo_profile->profile_image }}" alt="Product" width="40">
                              <span class="fs-xs c-primary ms-2">{{ $guests->neo->organization_name ?? $guests->rio->family_name.' '.$guests->rio->first_name}}</span>
                          </a>
                        </div>
                        <div class="vertical-right d-flex align-items-center justify-content-center">
                          <p class="fs-xs  m-0 p-2" >{{ $guests->equivalent_status }}</p>
                        </div>
                      </li>
                      @endforeach
                    </ul>
                  </div>
                </div>
                <!-- <div class="mb-3">
                  <label for="normal-input" class="form-label d-block">■{{ __('Issue video conference URL') }}</label>
                  <a class="ps-3 btn">{{ $schedule->meeting_url }}</a>
                </div> -->
                <div class="mb-3">
                  <label for="normal-input" class="form-label d-block">■{{ __('Explanation') }}</label>
                  <p class="m-0 ps-3">{!! nl2br($schedule->caption) !!}
                    </p>
                </div>
            </div>
          </div>
        </div>
      </div>
@endsection