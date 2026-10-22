@extends('layouts.app')

@section('title', trans('appeals::messages.create.title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1>{{ trans('appeals::messages.create.title') }}</h1>

            @if (! empty($introText))
                <div class="alert alert-secondary" style="white-space: pre-line;">{{ $introText }}</div>
            @endif

            @if (count($sanctions) > 1)
                <div class="alert alert-info">{{ trans('appeals::messages.create.multiple_sanctions') }}</div>
            @elseif ($sanction !== null)
                <div class="card shadow mb-4">
                    <div class="card-header">{{ trans('appeals::messages.sanctions.title') }}</div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">{{ trans('messages.fields.type') }}</dt>
                            <dd class="col-sm-8">{{ \Azuriom\Plugin\Appeals\Models\Appeal::sanctionTypeLabel($sanction->type) }}</dd>

                            <dt class="col-sm-4">{{ trans('appeals::messages.sanctions.reason') }}</dt>
                            <dd class="col-sm-8">{{ $sanction->reason ?? trans('appeals::messages.sanctions.unknown_reason') }}</dd>

                            <dt class="col-sm-4">{{ trans('appeals::messages.sanctions.staff') }}</dt>
                            <dd class="col-sm-8">{{ $sanction->staff ?? trans('appeals::messages.sanctions.unknown_staff') }}</dd>

                            <dt class="col-sm-4">{{ trans('messages.fields.date') }}</dt>
                            <dd class="col-sm-8">{{ $sanction->date !== null ? format_date($sanction->date) : trans('appeals::messages.sanctions.unknown_date') }}</dd>

                            <dt class="col-sm-4">{{ trans('appeals::messages.sanctions.duration') }}</dt>
                            <dd class="col-sm-8">{{ $sanction->durationLabel() }}</dd>
                        </dl>
                    </div>
                </div>
            @elseif (! $needsManualSanction)
                <div class="alert alert-info">{{ trans('appeals::messages.create.no_sanction_detected') }}</div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="{{ route('appeals.store') }}" method="POST">
                        @csrf

                        @if ($needsManualSanction)
                            <div class="alert alert-secondary">{{ trans('appeals::messages.create.manual_intro') }}</div>

                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="sanction_type">{{ trans('appeals::messages.create.manual_type') }}</label>
                                    <input type="text" class="form-control @error('sanction_type') is-invalid @enderror" id="sanction_type" name="sanction_type" value="{{ old('sanction_type') }}" maxlength="100" required="required" placeholder="{{ trans('appeals::messages.create.manual_type_placeholder') }}">
                                    @error('sanction_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="sanction_date">{{ trans('appeals::messages.create.manual_date') }}</label>
                                    <input type="date" class="form-control @error('sanction_date') is-invalid @enderror" id="sanction_date" name="sanction_date" value="{{ old('sanction_date') }}" max="{{ now()->format('Y-m-d') }}" required="required">
                                    @error('sanction_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="sanction_staff">{{ trans('appeals::messages.create.manual_staff') }}</label>
                                    <input type="text" class="form-control @error('sanction_staff') is-invalid @enderror" id="sanction_staff" name="sanction_staff" value="{{ old('sanction_staff') }}" maxlength="255">
                                    @error('sanction_staff')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="sanction_reason">{{ trans('appeals::messages.create.manual_sanction_reason') }}</label>
                                    <input type="text" class="form-control @error('sanction_reason') is-invalid @enderror" id="sanction_reason" name="sanction_reason" value="{{ old('sanction_reason') }}" maxlength="255">
                                    @error('sanction_reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif

                        @if (count($sanctions) > 1)
                            <div class="mb-3">
                                <label class="form-label" for="sanction_type">{{ trans('appeals::messages.create.choose_sanction') }}</label>
                                <select class="form-select @error('sanction_type') is-invalid @enderror" id="sanction_type" name="sanction_type" required="required">
                                    <option value="" disabled selected>{{ trans('appeals::messages.create.reason_placeholder') }}</option>
                                    @foreach ($sanctions as $type => $typeSanction)
                                        <option value="{{ $type }}" @selected(old('sanction_type') === $type)>
                                            {{ \Azuriom\Plugin\Appeals\Models\Appeal::sanctionTypeLabel($type) }} — {{ $typeSanction->reason ?? trans('appeals::messages.sanctions.unknown_reason') }} ({{ $typeSanction->durationLabel() }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('sanction_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label" for="reason_key">{{ trans('appeals::messages.create.reason') }}</label>
                            <select class="form-select @error('reason_key') is-invalid @enderror" id="reason_key" name="reason_key" required="required">
                                <option value="" disabled selected>{{ trans('appeals::messages.create.reason_placeholder') }}</option>
                                @foreach ($reasons as $reason)
                                    <option value="{{ $reason }}" @selected(old('reason_key') === $reason)>{{ $reason }}</option>
                                @endforeach
                            </select>
                            @error('reason_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="explanation">{{ trans('appeals::messages.create.explanation') }}</label>
                            <textarea class="form-control @error('explanation') is-invalid @enderror" id="explanation" name="explanation" rows="6" minlength="{{ $explanationMin }}" maxlength="{{ $explanationMax }}" oninput="appealsUpdateCharCount(this, 'explanation-count')" required="required">{{ old('explanation') }}</textarea>
                            <div class="form-text" id="explanation-count"></div>
                            @error('explanation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> {{ trans('appeals::messages.create.submit') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function appealsUpdateCharCount(textarea, counterId) {
                document.getElementById(counterId).textContent = textarea.value.length + ' / ' + textarea.maxLength;
            }

            window.addEventListener('load', function () {
                var textarea = document.getElementById('explanation');
                appealsUpdateCharCount(textarea, 'explanation-count');
            });
        </script>
    @endpush
@endsection
