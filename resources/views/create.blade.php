@extends('layouts.app')

@section('title', trans('appeals::messages.create.title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1>{{ trans('appeals::messages.create.title') }}</h1>

            @if (count($sanctions) > 1)
                <div class="alert alert-info">{{ trans('appeals::messages.create.multiple_sanctions') }}</div>
            @elseif ($sanction !== null)
                <div class="card shadow mb-4">
                    <div class="card-header">{{ trans('appeals::messages.sanctions.title') }}</div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">{{ trans('messages.fields.type') }}</dt>
                            <dd class="col-sm-8">{{ trans('appeals::messages.sanctions.types.'.$sanction->type) }}</dd>

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
            @else
                <div class="alert alert-info">{{ trans('appeals::messages.create.no_sanction_detected') }}</div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="{{ route('appeals.store') }}" method="POST">
                        @csrf

                        @if (count($sanctions) > 1)
                            <div class="mb-3">
                                <label class="form-label" for="sanction_type">{{ trans('appeals::messages.create.choose_sanction') }}</label>
                                <select class="form-select @error('sanction_type') is-invalid @enderror" id="sanction_type" name="sanction_type" required="required">
                                    <option value="" disabled selected>{{ trans('appeals::messages.create.reason_placeholder') }}</option>
                                    @foreach ($sanctions as $type => $typeSanction)
                                        <option value="{{ $type }}" @selected(old('sanction_type') === $type)>
                                            {{ trans('appeals::messages.sanctions.types.'.$type) }} — {{ $typeSanction->reason ?? trans('appeals::messages.sanctions.unknown_reason') }} ({{ $typeSanction->durationLabel() }})
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
                            <textarea class="form-control @error('explanation') is-invalid @enderror" id="explanation" name="explanation" rows="6" required="required">{{ old('explanation') }}</textarea>
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
@endsection
