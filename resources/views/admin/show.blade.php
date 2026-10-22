@extends('admin.layouts.admin')

@section('title', trans('appeals::admin.appeals.title'))

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('auto_action_info'))
        <div class="alert alert-info">{{ session('auto_action_info') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ $appeal->user?->name }}</span>
            <span class="badge bg-{{ \Azuriom\Plugin\Appeals\Models\Appeal::statusColor($appeal->status) }}">
                {{ trans('appeals::messages.statuses.'.$appeal->status) }}
            </span>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">{{ trans('appeals::messages.fields.reason') }}</dt>
                <dd class="col-sm-9">{{ $appeal->reason_key }}</dd>

                <dt class="col-sm-3">{{ trans('appeals::admin.appeals.explanation') }}</dt>
                <dd class="col-sm-9">{{ $appeal->explanation }}</dd>

                <dt class="col-sm-3">{{ trans('messages.fields.date') }}</dt>
                <dd class="col-sm-9">{{ format_date($appeal->created_at) }}</dd>
            </dl>

            @if ($appeal->sanction_type !== null)
                <hr>
                <h6>{{ trans('appeals::messages.sanctions.title') }}</h6>
                <dl class="row mb-0">
                    <dt class="col-sm-3">{{ trans('messages.fields.type') }}</dt>
                    <dd class="col-sm-9">{{ trans('appeals::messages.sanctions.types.'.$appeal->sanction_type) }}</dd>

                    <dt class="col-sm-3">{{ trans('appeals::messages.sanctions.reason') }}</dt>
                    <dd class="col-sm-9">{{ $appeal->sanction_reason ?? trans('appeals::messages.sanctions.unknown_reason') }}</dd>

                    <dt class="col-sm-3">{{ trans('appeals::messages.sanctions.staff') }}</dt>
                    <dd class="col-sm-9">{{ $appeal->sanction_staff ?? trans('appeals::messages.sanctions.unknown_staff') }}</dd>

                    <dt class="col-sm-3">{{ trans('appeals::messages.sanctions.duration') }}</dt>
                    <dd class="col-sm-9">{{ $appeal->sanction_duration ?? trans('appeals::messages.sanctions.unknown_duration') }}</dd>
                </dl>
            @endif

            @if ($appeal->handledBy !== null)
                <hr>
                <p class="text-muted mb-0">
                    {{ trans('appeals::admin.appeals.handled_by', ['name' => $appeal->handledBy->name, 'date' => format_date($appeal->handled_at)]) }}
                </p>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">{{ trans('appeals::admin.appeals.process') }}</div>
        <div class="card-body">
            <form action="{{ route('appeals.admin.update', $appeal) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="status">{{ trans('messages.fields.status') }}</label>
                    <select class="form-select" id="status" name="status" required="required">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected($appeal->status === $status)>
                                {{ trans('appeals::messages.statuses.'.$status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if (! empty($quickResponses))
                    <div class="mb-3">
                        <label class="form-label" for="quick_response_select">{{ trans('appeals::admin.appeals.quick_response') }}</label>
                        <select class="form-select" id="quick_response_select" onchange="appealsFillQuickResponse(this)">
                            <option value="">{{ trans('appeals::admin.appeals.quick_response_placeholder') }}</option>
                            @foreach ($quickResponses as $response)
                                <option value="{{ $response }}">{{ \Illuminate\Support\Str::limit($response, 80) }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label" for="staff_response">{{ trans('appeals::admin.appeals.response') }}</label>
                    <textarea class="form-control" id="staff_response" name="staff_response" rows="5">{{ old('staff_response', $appeal->staff_response) }}</textarea>
                </div>

                @if ($autoActionEnabled && $appeal->sanction_type !== null)
                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" id="auto_action" name="auto_action" value="lift">
                        <label class="form-check-label" for="auto_action">{{ trans('appeals::admin.appeals.auto_action_lift') }}</label>
                        <div class="form-text">{{ trans('appeals::admin.appeals.auto_action_help') }}</div>
                    </div>
                @endif

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ trans('messages.actions.save') }}
                </button>
            </form>
        </div>
    </div>

    <a href="{{ route('appeals.admin.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> {{ trans('messages.actions.back') }}
    </a>

    <form action="{{ route('appeals.admin.destroy', $appeal) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ trans('appeals::admin.appeals.delete_confirm') }}')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="bi bi-trash"></i> {{ trans('messages.actions.delete') }}
        </button>
    </form>

    @if (! empty($quickResponses))
        @push('scripts')
            <script>
                function appealsFillQuickResponse(select) {
                    if (select.value) {
                        document.getElementById('staff_response').value = select.value;
                    }
                }
            </script>
        @endpush
    @endif
@endsection
