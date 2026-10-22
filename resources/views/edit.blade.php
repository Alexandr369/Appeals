@extends('layouts.app')

@section('title', trans('appeals::messages.edit.title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1>{{ trans('appeals::messages.edit.title') }}</h1>

            @if ($appeal->sanction_type !== null)
                <div class="card shadow mb-4">
                    <div class="card-header">{{ trans('appeals::messages.sanctions.title') }}</div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">{{ trans('messages.fields.type') }}</dt>
                            <dd class="col-sm-8">{{ trans('appeals::messages.sanctions.types.'.$appeal->sanction_type) }}</dd>

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
                            <textarea class="form-control @error('explanation') is-invalid @enderror" id="explanation" name="explanation" rows="6" required="required">{{ old('explanation', $appeal->explanation) }}</textarea>
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
@endsection
