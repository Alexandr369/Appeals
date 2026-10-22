@extends('layouts.app')

@section('title', trans('appeals::messages.show.title'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="mb-0">{{ trans('appeals::messages.show.title') }}</h1>
                <span class="badge bg-{{ \Azuriom\Plugin\Appeals\Models\Appeal::statusColor($appeal->status) }} fs-6">
                    {{ trans('appeals::messages.statuses.'.$appeal->status) }}
                </span>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header">{{ trans('appeals::messages.fields.reason') }}</div>
                <div class="card-body">
                    <p><strong>{{ $appeal->reason_key }}</strong></p>
                    <p class="mb-0">{{ $appeal->explanation }}</p>
                </div>
            </div>

            @if ($appeal->sanction_type !== null)
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

                            <dt class="col-sm-4">{{ trans('messages.fields.date') }}</dt>
                            <dd class="col-sm-8">{{ $appeal->sanction_date !== null ? format_date($appeal->sanction_date) : trans('appeals::messages.sanctions.unknown_date') }}</dd>

                            <dt class="col-sm-4">{{ trans('appeals::messages.sanctions.duration') }}</dt>
                            <dd class="col-sm-8">{{ $appeal->sanction_duration ?? trans('appeals::messages.sanctions.unknown_duration') }}</dd>
                        </dl>
                    </div>
                </div>
            @endif

            @if ($appeal->isHandled())
                <div class="card shadow mb-4">
                    <div class="card-header">{{ trans('appeals::messages.show.staff_response') }}</div>
                    <div class="card-body">
                        <p class="mb-0">{{ $appeal->staff_response }}</p>
                    </div>
                </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-header">{{ trans('appeals::messages.show.history') }}</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="badge bg-{{ \Azuriom\Plugin\Appeals\Models\Appeal::statusColor('pending') }}">
                            {{ trans('appeals::messages.statuses.pending') }}
                        </span>
                        <span class="text-muted small">{{ format_date($appeal->created_at, true) }}</span>
                    </li>
                    @foreach ($appeal->statusHistory as $entry)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="badge bg-{{ \Azuriom\Plugin\Appeals\Models\Appeal::statusColor($entry->status) }}">
                                {{ trans('appeals::messages.statuses.'.$entry->status) }}
                            </span>
                            <span class="text-muted small">{{ format_date($entry->created_at, true) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <a href="{{ route('appeals.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> {{ trans('messages.actions.back') }}
            </a>

            @can('appeals.edit')
                @if ($appeal->isActive())
                    <a href="{{ route('appeals.edit', $appeal) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> {{ trans('messages.actions.edit') }}
                    </a>
                @endif
            @endcan
        </div>
    </div>
@endsection
