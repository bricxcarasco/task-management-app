@extends('layouts.main')

@section('content')
<div class="container position-relative pt-6 py-md-6 mb-md-3 home--height" >
        {{-- Alert --}}
        @include('components.alert')
        <div class="row">
          <!-- Content-->
            <div class="col-12 col-md-9 offset-md-3">
                <div class="d-flex flex-column rounded-3 ">
                    <div class="border-bottom position-relative">
                        <h3 class="p-3 mb-0 text-center">{{ __('HERO introduction URL') }}</h3>
                    </div>
                    <div class="mt-4">
                        <!-- <p class="p-4 text-center">{{ __('Please introduce HERO to your friends and acquaintances') }}
                        </p>
                        <div class="bg-gray px-4 py-3">
                            <dl>
                                <dt>{{ __('Benefits') }}1</dt>
                                <dd>テキストテキストテキストテキスト</dd>
                                <dt>{{ __('Benefits') }}2</dt>
                                <dd>テキストテキストテキストテキスト</dd>
                            </dl>
                        </div> -->

                        <p class="mt-4 mb-0">{{ __('Your referral URL') }}</p>
                        <div class="d-flex align-items-center justify-content-between text-break" >
                            <div>
                                <a href="#" id="referral-link">{{ route('registration.email').'/'.$rio->referral_code }}</a>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-link p-0" id="copy-referral-link" data-bs-original-title="{{ __('Copied') }}">{{ __('Copy') }}</a>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('rio.introduce_hero.update') }}" novalidate>
                            @csrf
                            <p class="mt-4 mb-2">{{ __('Introductory text template') }}</p>
                            <textarea class="form-control @error('referral_message_template') is-invalid @enderror" rows="6" id="referral-message" name="referral_message_template" >{{ $rio->referral_message_template }} </textarea>
                            <div class="text-end">
                                <button type="button" class="btn btn-link p-0" id="copy-template" data-bs-original-title="{{ __('Copied') }}">{{ __('Copy') }}</button>
                            </div>
                            @error('referral_message_template')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                            <div class="my-4 text-center">
                                <button type="submit" class="btn btn-primary" style="width: 200px;">{{ __('Save edited text') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
      </div>
@endsection
@push('js')
<script>
    $(function () {
        function setTooltip(id){
            $(id).tooltip('show');
        }

        $('button').mouseover(function() {
            $(this).tooltip('dispose');
        });

        function hideTooltip(id) {
            setTimeout(function() {
                $(id).tooltip('hide');
            }, 3000);
        }

        $(document).on('click', '#copy-referral-link', function(event){
            var copyLink = document.querySelector('#referral-link');
            var range = document.createRange();
            range.selectNode(copyLink);
            window.getSelection().addRange(range);

            document.execCommand('copy');

            setTooltip('#copy-referral-link');
            hideTooltip('#copy-referral-link');

            window.getSelection().removeAllRanges();
        })

        $(document).on('click', '#copy-template', function(event){
            var copyLink = document.querySelector('#referral-message');
            var range = document.createRange();
            range.selectNode(copyLink);
            window.getSelection().addRange(range);

            document.execCommand('copy');

            setTooltip('#copy-template');
            hideTooltip('#copy-template');

            window.getSelection().removeAllRanges();
        })

        $(document).on('click', '#referral-link', function(event){
            return false;
        })

    });
</script>
@endpush