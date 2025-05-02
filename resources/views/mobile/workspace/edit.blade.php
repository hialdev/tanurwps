@extends('layouts.mobile')

@section('css')
<link rel="stylesheet" href="/assets/libs/select2/dist/css/select2.min.css">

<style>
.baseapp{
    position: relative;
    background: linear-gradient(180deg, #133057 0%, #065F6A 23.43%, #67ACAC 54.38%, #C7E0E0 72.07%, rgba(255, 255, 255, 0.00) 91.97%);
}
.bg-gradient-tanur{
    background: linear-gradient(180deg, #133057 0%, #065F6A 23.43%, #67ACAC 54.38%, #C7E0E0 72.07%, rgba(255, 255, 255, 0.00) 91.97%);
}
.select2-selection__rendered, .select2-results__option{
    font-size: 12px;
}
@media (max-width: 576px) {
    .baseapp {
        max-height: auto;
    }
}
</style>
@endsection

@section('content')
<div class="">
    <div class="p-3 bg-tanur-green pb-4" style="margin-bottom: -0.7rem">
        <h1 class="fw-semibold fs-3 mb-0 text-white text-center text-uppercase">Edit Workspace</h1>
    </div>
    <section class="bg-white rounded-top-3 bg-dark">
        <form action="{{ route('agent.workspace.update', [$workspace->agent_id, $workspace->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="bg-white rounded-top-3 p-3 pb-0" style="z-index: 99">
                <div class="mb-2">
                    <label for="name" class="fs-1 text-muted form-label">Nama Workspace</label>
                    <input type="text" class="form-control fs-2" placeholder="Nama Workspace" id="name" name="name" value="{{ old('name', $workspace->name) }}" required>
                </div>
                <div class="mb-2">
                    <label for="description" class="fs-1 text-muted form-label">Deskripsi / Keterangan</label>
                    <textarea class="form-control fs-2" rows="4" cols="10" placeholder="Keterangan" id="description" name="description">{{ old('description', $workspace->description) }}</textarea>
                </div>
                <div class="mb-2">
                    <label for="product_type" class="fs-1 text-muted form-label">Tipe Produk</label>
                    <div class="d-flex gap-2">
                        <input type="radio" class="btn-check" name="product_type" id="umroh" value="umroh" {{ old('product_type', $workspace->product_type) == 'umroh' ? 'checked' : '' }} required>
                        <label class="btn btn-outline-dark fs-2" for="umroh">Umroh</label>

                        <input type="radio" class="btn-check" name="product_type" id="haji" value="haji" {{ old('product_type', $workspace->product_type) == 'haji' ? 'checked' : '' }} required>
                        <label class="btn btn-outline-dark fs-2" for="haji">Haji</label>
                    </div>
                </div>

                <div class="fs-1 text-muted form-label">Alamat / Area</div>
                <div class="mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-map-pin fs-4 text-dark"></i>
                    <textarea class="form-control fs-2 bg-white" rows="2" cols="10" placeholder="Alamat Lengkap" id="address" name="address">{{ old('address', $workspace->address) }}</textarea>
                </div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="ti ti-building-estate fs-4 text-dark"></i>
                    <div style="flex-grow:1">
                        <select name="city" id="city" class="select2 bg-white fs-2 form-select">
                            <option value="">-- Pilih Kota / Kabupaten --</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->nama }}" {{ old('city', $workspace->city) == $city->nama ? 'selected' : '' }}>{{ $city->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-zip fs-4 text-dark"></i>
                    <input type="number" class="form-control fs-2 bg-white" placeholder="ZIP / Postal Code" id="postal_code" name="postal_code" value="{{ old('postal_code', $workspace->postal_code) }}" required>
                </div>

                <div class="fs-1 text-muted form-label mt-2">PIC</div>
                <div class="mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-user-circle fs-4 text-dark"></i>
                    <input type="text" class="form-control fs-2 bg-white" placeholder="Nama PIC" id="pic_name" name="pic_name" value="{{ old('pic_name', $workspace->pic_name) }}" required>
                </div>
                <div class="mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-phone fs-4 text-dark"></i>
                    <input type="number" class="form-control fs-2 bg-white" placeholder="Phone" id="pic_phone" name="pic_phone" value="{{ old('pic_phone', $workspace->pic_phone) }}" required>
                </div>
                <div class="mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-mail fs-4 text-dark"></i>
                    <input type="email" class="form-control fs-2 bg-white" placeholder="Email" id="pic_email" name="pic_email" value="{{ old('pic_email', $workspace->pic_email) }}">
                </div>

                <div class="d-flex align-items-center mt-3 mb-2 justify-content-between">
                    <h6 class="fs-2 fw-semibold mb-0">Calon Jamaah</h6>
                    <button type="button" class="btn-add-pilgrims btn d-flex align-items-center justify-content-center p-1 btn-primary btn-sm rounded-circle"><i class="ti ti-plus"></i></button>
                </div>
                <div id="jamaahNullList" class="p-3 border border-2 rounded-3 fs-2 bg-light border-dashed text-center {{ $workspace->pilgrims->count() ? 'd-none' : '' }}">
                    Belum ada Calon Jamaah yang ditambahkan, Klik tombol + diatas untuk menambahkannya
                </div>
                <div class="" id="jamaahList">
                    @foreach ($workspace->pilgrims as $i => $pilgrim)
                        <div class="p-3 rounded-3 bg-white mb-2 border border-2 border-dashed position-relative">
                            <div class="position-absolute top-0 end-0 m-1 d-flex align-items-center gap-1">
                                <button type="button" class="btn btn-sm bg-danger-subtle text-danger d-flex align-items-center justify-content-center p-1 btn-remove-jamaah"><i class="ti ti-trash"></i></button>
                            </div>
                            <input type="hidden" name="jamaah[{{ $i }}][name]" value="{{ $pilgrim->name }}">
                            <input type="hidden" name="jamaah[{{ $i }}][phone]" value="{{ $pilgrim->phone }}">
                            <input type="hidden" name="jamaah[{{ $i }}][email]" value="{{ $pilgrim->email }}">
                            <div class="fs-2 mb-1 d-flex align-items-center gap-2"><i class="ti ti-user-circle me-2"></i>{{ $pilgrim->name }}</div>
                            <div class="fs-2 mb-1 d-flex align-items-center gap-2"><i class="ti ti-phone me-2"></i>{{ $pilgrim->phone }}</div>
                            <div class="fs-2 mb-1 d-flex align-items-center gap-2"><i class="ti ti-mail me-2"></i>{{ $pilgrim->email }}</div>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn shadow-lg mt-3 bg-tanur-green fw-semibold text-white border-0 w-100">Perbarui Workspace <i class="ti ti-send ms-2"></i></button>
            </div>
        </form>
    </section>

    {{-- Modal Tambah Jamaah --}}
    <div id="addJamaahModal" style="display: none; max-width:576px;" class="bg-white rounded-top-3 mx-auto pt-3 position-fixed bottom-0 start-0 end-0 shadow-lg mx-auto">
        <div class="d-flex mb-3 px-3 align-items-center justify-content-between">
            <h6 class="fs-3 mb-0">Tambah Calon Jamaah</h6>
            <button class="btn-close-modal btn btn-danger-subtle p-1 btn-sm"><i class="ti ti-x fs-3"></i></button>
        </div>
        <div class="p-3 rounded-top-3 bg-light">
            <div class="mb-1 d-flex align-items-center gap-2">
                <i class="ti ti-user-circle fs-4 text-dark"></i>
                <input type="text" class="form-control fs-2 bg-white" placeholder="Nama Jamaah" id="name_pilgrims" name="name_pilgrims[]" required>
            </div>
            <div class="mb-1 d-flex align-items-center gap-2">
                <i class="ti ti-phone fs-4 text-dark"></i>
                <input type="number" class="form-control fs-2 bg-white" placeholder="Phone" id="phone_pilgrims" name="phone_pilgrims[]" required>
            </div>
            <div class="mb-1 d-flex align-items-center gap-2">
                <i class="ti ti-mail fs-4 text-dark"></i>
                <input type="email" class="form-control fs-2 bg-white" placeholder="Email" id="email_pilgrims" name="email_pilgrims[]" required>
            </div>

            <button class="btn-save-pilgrims rounded-pill btn btn-primary btn-sm w-100 mt-2">Tambah Jamaah</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="/assets/libs/select2/dist/js/select2.full.min.js"></script>
<script>
$(document).ready(function () {
    var addJamaahModal = $('#addJamaahModal');
    var btnAddJamaah = $('.btn-add-pilgrims');
    var btnCloseModal = $('.btn-close-modal');
    var btnSaveJamaah = $('.btn-save-pilgrims');
    var nameJamaah = $('#name_pilgrims');
    var phoneJamaah = $('#phone_pilgrims');
    var emailJamaah = $('#email_pilgrims');
    var jamaahIndex = {{ $workspace->pilgrims->count() }};

    $('.select2').select2();

    btnAddJamaah.on('click', function () {
        $('.mobile-menu').hide();
        addJamaahModal.slideToggle(300);
    });
    btnCloseModal.on('click', function () {
        addJamaahModal.slideToggle(300);
        $('.mobile-menu').show();
    });

    btnSaveJamaah.on('click', function () {
        var name = nameJamaah.val();
        var phone = phoneJamaah.val();
        var email = emailJamaah.val();

        if (!name || !phone) {
            alert('Nama dan No. HP wajib diisi.');
            return;
        }

        const html = `
            <div class="p-3 rounded-3 bg-white mb-2 border border-2 border-dashed position-relative">
                <div class="position-absolute top-0 end-0 m-1 d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-sm bg-danger-subtle text-danger d-flex align-items-center justify-content-center p-1 btn-remove-jamaah"><i class="ti ti-trash"></i></button>
                </div>
                <input type="hidden" name="jamaah[${jamaahIndex}][name]" value="${name}">
                <input type="hidden" name="jamaah[${jamaahIndex}][phone]" value="${phone}">
                <input type="hidden" name="jamaah[${jamaahIndex}][email]" value="${email}">
                <div class="fs-2 mb-1 d-flex align-items-center gap-2"><i class="ti ti-user-circle me-2"></i>${name}</div>
                <div class="fs-2 mb-1 d-flex align-items-center gap-2"><i class="ti ti-phone me-2"></i>${phone}</div>
                <div class="fs-2 mb-1 d-flex align-items-center gap-2"><i class="ti ti-mail me-2"></i>${email}</div>
            </div>
        `;
        $('#jamaahNullList').addClass('d-none');
        $('#jamaahList').append(html);
        jamaahIndex++;

        nameJamaah.val('');
        phoneJamaah.val('');
        emailJamaah.val('');

        addJamaahModal.slideUp(300);
    });

    $(document).on('click', '.btn-remove-jamaah', function () {
        $(this).closest('.p-3').remove();
    });
});
</script>
@endsection
