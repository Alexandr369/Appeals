@extends('layouts.app')

@section('title', trans('appeals::messages.edit.title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1>{{ trans('appeals::messages.edit.title') }}</h1>

            @if ($appeal->sanction_type !== null && $appeal->sanction_source !== 'manual')
                <div class="card shadow mb-4">
                    <div class="card-header">{{ trans('appeals::messages.sanctions.title') }}</div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">{{ trans('messages.fields.type') }}</dt>
                            <dd class="col-sm-8">{{ \Azuriom\Plugin\Appeals\Models\Appeal::sanctionTypeLabel($appeal->sanction_type) }}</dd>

                            <dt class="col-sm-4">{{ trans('appeals::messages.sanctions.reason') }}</dt>
                            <dd class="col-sm-8">{{ $appeal->sanction_reason ?? trans('appeals::messages.sanctions.unknown_reason') }}</dd>

                            <dt class="col-sm-4">{{ trans('appeals::messages.sanctions.staff') }}</dt>
                            <dd class="col-sm-8">{{ $appeal->sanction_staff ?? trans('appeals::messages.sanctions.unknown_staff') }}</dd>

                            <dt class="col-sm-4">{{ trans('appeals::messages.sanctions.duration') }}</dt>
                            <dd class="col-sm-8">{{ $appeal->sanction_duration ?? trans('appeals::messages.sanctions.unknown_duration') }}</dd>
                        </dl>
                    </div>
                </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="{{ route('appeals.update', $appeal) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @if ($appeal->sanction_source === 'manual')
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="sanction_type">{{ trans('appeals::messages.create.manual_type') }}</label>
                                    <input type="text" class="form-control @error('sanction_type') is-invalid @enderror" id="sanction_type" name="sanction_type" value="{{ old('sanction_type', $appeal->sanction_type) }}" maxlength="100" required="required">
                                    @error('sanction_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="sanction_date">{{ trans('appeals::messages.create.manual_date') }}</label>
                                    <input type="date" class="form-control @error('sanction_date') is-invalid @enderror" id="sanction_date" name="sanction_date" value="{{ old('sanction_date', $appeal->sanction_date?->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}" required="required">
                                    @error('sanction_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="sanction_staff">{{ trans('appeals::messages.create.manual_staff') }}</label>
                                    <input type="text" class="form-control @error('sanction_staff') is-invalid @enderror" id="sanction_staff" name="sanction_staff" value="{{ old('sanction_staff', $appeal->sanction_staff) }}" maxlength="255">
                                    @error('sanction_staff')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="sanction_reason">{{ trans('appeals::messages.create.manual_sanction_reason') }}</label>
                                    <input type="text" class="form-control @error('sanction_reason') is-invalid @enderror" id="sanction_reason" name="sanction_reason" value="{{ old('sanction_reason', $appeal->sanction_reason) }}" maxlength="255">
                                    @error('sanction_reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label" for="reason_key">{{ trans('appeals::messages.create.reason') }}</label>
                            <select class="form-select @error('reason_key') is-invalid @enderror" id="reason_key" name="reason_key" required="required">
                                <option value="" disabled>{{ trans('appeals::messages.create.reason_placeholder') }}</option>
                                @foreach ($reasons as $reason)
                                    <option value="{{ $reason }}" @selected(old('reason_key', $appeal->reason_key) === $reason)>{{ $reason }}</option>
                                @endforeach
                            </select>
                            @error('reason_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="explanation">{{ trans('appeals::messages.create.explanation') }}</label>
                            <textarea class="form-control @error('explanation') is-invalid @enderror" id="explanation" name="explanation" rows="6" minlength="{{ $explanationMin }}" maxlength="{{ $explanationMax }}" oninput="appealsUpdateCharCount(this, 'explanation-count')" required="required">{{ old('explanation', $appeal->explanation) }}</textarea>
                            <div class="form-text" id="explanation-count"></div>
                            @error('explanation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> {{ trans('messages.actions.save') }}
                        </button>
                        <a href="{{ route('appeals.show', $appeal) }}" class="btn btn-secondary">
                            {{ trans('messages.actions.cancel') }}
                        </a>
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
