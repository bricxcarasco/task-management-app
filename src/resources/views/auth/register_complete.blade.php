@extends('layouts.guest')

@section('content')
<section class="container d-flex align-items-center pt-7 pb-3 pb-md-4 h-md-100" style="flex: 1 0 auto;">
    <div class="w-100 pt-3 pt-md-0">
        <div class="row justify-content-center pt-4">
            <div class="col-lg-7 col-md-9 col-sm-11">
                <h1 class="h2 pb-3 text-md-center">{{ __('Membership registration is complete.') }}</h1>
                <div class="bg-white rounded-3 px-3 py-4 p-sm-4">              
                    <p class="fs-sm">{{ $email }}{{ __('Membership registration completion email has been sent to 〇〇@XXX.com. Please log in with the registered e-mail address and password from the login page.') }}
                    </p>
                    <div class="text-center mt-4 pt-4">
                        <div class="row pt-2">
                            <div class="col-md-6 offset-md-3 pt-2">
                                <a href="{{ route('login.get') }}" class="btn btn-primary d-block w-100 mb-3">{{ __('Login Page') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
