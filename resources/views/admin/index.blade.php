@extends('admin.layouts.admin')

@section('title', trans('appeals::admin.nav.appeals'))

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-auto">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">{{ trans('appeals::admin.appeals.all_statuses') }}</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>
                                {{ trans('appeals::messages.statuses.'.$status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-auto">
                    <div class="input-group">
                        <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ trans('messages.actions.search') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-wrapper table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">{{ trans('appeals::admin.appeals.user') }}</th>
                            <th scope="col">{{ trans('appeals::messages.fields.reason') }}</th>
                            <th scope="col">{{ trans('messages.fields.date') }}</th>
                            <th scope="col">{{ trans('messages.fields.status') }}</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($appeals as $appeal)
                            <tr>
                                <td>{{ $appeal->user?->name }}</td>
                                <td>{{ $appeal->reason_key }}</td>
                                <td>{{ format_date($appeal->created_at) }}</td>
                                <td>
                                    <span class="badge bg-{{ \Azuriom\Plugin\Appeals\Models\Appeal::statusColor($appeal->status) }}">
                                        {{ trans('appeals::messages.statuses.'.$appeal->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('appeals.admin.show', $appeal) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">{{ trans('appeals::admin.appeals.no_appeals') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $appeals->links() }}
            </div>
        </div>
    </div>
@endsection
