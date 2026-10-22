@extends('layouts.app')

@section('title', trans('appeals::messages.title'))

@section('content')
    <div class="row align-items-center mb-3">
        <div class="col">
            <h1 class="mb-0">{{ trans('appeals::messages.title') }}</h1>
        </div>

        <div class="col-auto">
            @if ($activeAppeal !== null)
                <a href="{{ route('appeals.show', $activeAppeal) }}" class="btn btn-primary">
                    {{ trans('appeals::messages.index.view_active') }}
                </a>
            @elseif ($nextAllowedAt !== null)
                <button type="button" class="btn btn-secondary" disabled="disabled">
                    {{ trans('appeals::messages.index.blocked_until', ['date' => format_date($nextAllowedAt)]) }}
                </button>
            @else
                <a href="{{ route('appeals.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> {{ trans('appeals::messages.index.create') }}
                </a>
            @endif
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-wrapper table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">{{ trans('appeals::messages.fields.reason') }}</th>
                    <th scope="col">{{ trans('messages.fields.date') }}</th>
                    <th scope="col">{{ trans('messages.fields.status') }}</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($appeals as $appeal)
                    <tr>
                        <td>{{ $appeal->reason_key }}</td>
                        <td>{{ format_date($appeal->created_at) }}</td>
                        <td>
                            <span class="badge bg-{{ \Azuriom\Plugin\Appeals\Models\Appeal::statusColor($appeal->status) }}">
                                {{ trans('appeals::messages.statuses.'.$appeal->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('appeals.show', $appeal) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">{{ trans('appeals::messages.index.no_appeals') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $appeals->links() }}
    </div>
@endsection
