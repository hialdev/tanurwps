@extends('layouts.base')
@section('content')
    <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Agents</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-muted text-decoration-none" href="{{ route('home') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Semua agent</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-3">
                    <div class="text-center mb-n5">
                        <img src="/assets/images/breadcrumb/ChatBc.png" alt="" class="img-fluid mb-n4" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-3 d-flex align-items-center gap-2 justify-content-between">
        <h1>Agents</h1>
        <div style=""
            class="bg-primary fw-semibold px-3 p-2 text-white d-flex align-items-center justify-content-center rounded-5 me-auto">
            {{ $agents->total() }}</div>
    </div>
    <div>
        <form action="{{ url()->current() }}" method="GET">
            <div class="row align-items-end mb-3 flex-wrap">
                <div class="col-md-4 mb-2">
                    <label for="search" class="form-label">Filter Kata</label>
                    <input type="text" class="form-control" placeholder="Cari agent" name="search"
                        value="{{ $filter->q ?? '' }}">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="field" class="form-label">Urutkan Berdasarkan</label>
                    <select name="field" id="field" class="form-select">
                        @foreach ($fields as $atr)
                            <option value="{{ $atr }}" {{ $filter->field == $atr ? 'selected' : '' }}>
                                {{ toPascalCase($atr) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label for="order" class="form-label">Dengan urutan</label>
                    <select name="order" id="order" class="form-select">
                        <option value="newest" {{ $filter->order == 'desc' ? 'selected' : '' }}>Terbaru / Terbesar
                        </option>
                        <option value="oldest" {{ $filter->order == 'asc' ? 'selected' : '' }}>Terlama / Terkecil
                        </option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <div class="d-flex align-items-center gap-1">
                        <button type="submit" class="btn btn-primary w-100" style="white-space: nowrap">Apply</button>
                        <a href="{{ url()->current() }}" class="btn btn-secondary" style="white-space: nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M22 12c0 5.523-4.477 10-10 10S2 17.523 2 12S6.477 2 12 2v2a8 8 0 1 0 4.5 1.385V8h-2V2h6v2H18a9.99 9.99 0 0 1 4 8" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="table-responsive mb-4">
        <table class="table border text-nowrap mb-0 align-middle">
            <thead class="text-dark fs-4">
                <tr>
                    <th><h6 class="fs-4 fw-semibold mb-0">Agent</h6></th>
                    <th><h6 class="fs-4 fw-semibold mb-0">Detail</h6></th>
                    <th><h6 class="fs-4 fw-semibold mb-0">Level</h6></th>
                    <th><h6 class="fs-4 fw-semibold mb-0">Keanggotaan</h6></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($agents as $agent)
                <tr>
                    <td>
                        <div>
                            <div class="badge bg-primary-subtle text-primary fw-semibold fs-1 mb-1">Agent No: {{ $agent['agent_no'] }}</div>
                            <div class="fw-normal fs-1 text-muted">Nama Agent</div>
                            <h6 class="fw-semibold text-dark mb-1 fs-3">{{ $agent['name'] }}</h6>
                        </div>
                        <div>
                            <div class="fw-normal fs-1 text-muted">Referral Code</div>
                            <div class="text-primary fs-2 fw-semibold mb-1">{{ $agent['referral_code'] }}</div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <div class="text-dark fs-1 mb-1">
                                <img src="{{ $agent['image_url'] }}" alt="Foto Agent" width="50" style="aspect-ratio:1/1;" class="rounded-3">
                            </div>
                        </div>
                        <div>
                            <div class="fw-normal fs-1 text-muted">Gender</div>
                            <div class="text-dark fs-1 mb-1">{{ $agent['gender'] === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="badge bg-info-subtle text-info fw-semibold fs-1 mb-1">ID Level: {{ $agent['id_level'] }}</div>
                        <h6 class="fw-semibold fs-3 text-dark mb-1">{{ $agent['level'] }}</h6>
                    </td>
                    <td>
                        <div class="d-flex flex-column align-items-start gap-2">
                            <div class="badge bg-primary text-white rounded-3 fw-semibold fs-2">Joined: {{ \Carbon\Carbon::parse($agent['date_joined'])->format('d M Y') }}</div>
                            @if (!empty($agent['date_mutation']))
                                <div class="badge bg-danger text-white rounded-3 fw-semibold fs-2">Mutasi: {{ \Carbon\Carbon::parse($agent['date_mutation'])->format('d M Y') }}</div>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="dropdown dropstart">
                            <a href="#" class="text-muted" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-dots fs-5"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li>
                                    <a href="{{route('agent.workspace.index', $agent['id'])}}" class="dropdown-item d-flex bg-primary text-white align-items-center gap-3"><i class="fs-4 ti ti-analyze"></i>Workspace</a>
                                </li>
                                <li>
                                    <a href="#" class="dropdown-item d-flex bg-primary-subtle text-dark align-items-center gap-3"><i class="fs-4 ti ti-list-check"></i>Approval</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data agent ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    <div>
        <nav aria-label="Pagination">
            <ul class="pagination">
                <!-- Previous Button -->
                @if ($agents->currentPage() > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $agents->previousPageUrl() }}" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link" aria-hidden="true">&laquo;</span>
                    </li>
                @endif

                <!-- Page Number Links -->
                @for ($i = 1; $i <= $agents->lastPage(); $i++)
                    <li class="page-item {{ $i == $agents->currentPage() ? 'active' : '' }}">
                        <a class="page-link {{ $i == $agents->currentPage() ? 'bg-primary border-primary' : '' }}" href="{{ $agents->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor

                <!-- Next Button -->
                @if ($agents->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $agents->nextPageUrl() }}" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link" aria-hidden="true">&raquo;</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $('.delete-attachment-btn').on('click', function () {
        var agentId = $(this).data('agent-id');
        var attachmentId = $(this).data('attachment-id');
        var attachmentName = $(this).data('attachment-name');

        // Update text di modal
        $('#deleteAttachmentMessage').text('Apakah Anda yakin ingin menghapus file attachment "' + attachmentName + '"?');

        // Update form action
        $('#deleteAttachmentForm').attr('action', '/agents/' + agentId + '/attachments/' + attachmentId);
    });
});
</script>

@endsection
