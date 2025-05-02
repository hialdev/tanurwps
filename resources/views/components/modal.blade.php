<div id="{{ $id }}" style="display: none; max-width:576px; z-index:100" class="modal-custom bg-white rounded-top-3 mx-auto pt-3 position-fixed bottom-0 start-0 end-0 shadow-lg" data-modal-id="{{ $id }}">
    <div class="d-flex mb-3 px-3 align-items-center justify-content-between">
        <h6 class="fs-3 mb-0">{{ $title }}</h6>
        <button type="button" class="btn-close-modal btn btn-danger-subtle p-1 btn-sm" data-modal-id="{{ $id }}"><i class="ti ti-x fs-3"></i></button>
    </div>

    <div class="p-3 rounded-top-3 bg-light">
        {{-- Content ditentukan saat include --}}
        {{ $slot }}
    </div>
</div>
