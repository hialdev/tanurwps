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
/* Responsive: hilangkan frame saat layar kecil */
@media (max-width: 576px) {
    .baseapp {
        max-height: auto;
    }
}
</style>
@endsection

@section('content')
<div class="" style="position: relative">
    <div class="p-3 bg-tanur-green pb-4" style="margin-bottom: -0.7rem">
        <h1 class="fw-semibold fs-3 mb-0 text-white text-center text-uppercase">Tambah Workspace</h1>
    </div>
    <section class="bg-white rounded-top-3 bg-dark">
        <form action="{{ route('agent.workspace.store', request()->route('id')) }}" method="POST">
            @csrf
            <div class="bg-white rounded-top-3 p-3 pb-0" style="z-index: 99">
                <div class="mb-2">
                    <label for="name" class="fs-3 text-muted form-label">Nama Workspace</label>
                    <input type="text" class="form-control form-control-lg fs-3" placeholder="Nama Workspace" id="name" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="mb-2">
                    <label for="description" class="fs-3 text-muted form-label">Deskripsi / Keterangan</label>
                    <textarea class="form-control form-control-lg fs-3" rows="4" cols="10" placeholder="Keterangan" id="description" name="description">{{ old('description') }}</textarea>
                </div>
                <div class="mb-2">
                    <label for="product_type" class="fs-3 text-muted form-label">Tipe Produk</label>
                    <div class="d-flex gap-2">
                        <input type="radio" class="btn-check" name="product_type" id="umroh" value="umroh" {{old('product_type') == 'umroh' ? 'checked' : ''}} required>
                        <label class="btn btn-outline-dark fs-3" for="umroh">Umroh</label>

                        <input type="radio" class="btn-check" name="product_type" id="haji" value="haji" {{old('product_type') == 'haji' ? 'checked' : ''}} required>
                        <label class="btn btn-outline-dark fs-3" for="haji">Haji</label>
                    </div>
                </div>

                <div class="fs-3 text-muted form-label">Alamat / Area</div>
                <div class="mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-map-pin fs-4 text-dark"></i>
                    <textarea class="form-control form-control-lg fs-3 bg-white" rows="2" cols="10" placeholder="Alamat Lengkap" id="address" name="address">{{ old('address') }}</textarea>
                </div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="ti ti-building-estate fs-4 text-dark"></i>
                    <div style="flex-grow:1">
                        <select name="city" id="city" class="select2 bg-white fs-3 form-select">
                            <option value="">-- Pilih Kota / Kabupaten --</option>
                            
                            @foreach ($cities as $city)
                                <option value="{{ $city->nama }}" {{ old('city') == $city->nama ? 'selected' : '' }}>{{ $city->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="fs-3 text-muted form-label mt-2">PIC</div>
                <div class="mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-user-circle fs-4 text-dark"></i>
                    <input type="text" class="form-control form-control-lg fs-3 bg-white" placeholder="Nama PIC" id="pic_name" name="pic_name" value="{{old('pic_name')}}" required>
                </div>
                <div class="mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-phone fs-4 text-dark"></i>
                    <input type="number" class="form-control form-control-lg fs-3 bg-white" placeholder="Phone" id="pic_phone" name="pic_phone" value="{{old('pic_phone')}}" required>
                </div>
                <div class="mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-mail fs-4 text-dark"></i>
                    <input type="email" class="form-control form-control-lg fs-3 bg-white" placeholder="Email" id="pic_email" name="pic_email" value="{{old('pic_email')}}">
                </div>
                
                <div class="fs-3 text-muted form-label mt-2">Total Jamaah</div>
                <div class="mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-gender-male fs-4 text-dark"></i>
                    <input type="number" class="form-control form-control-lg fs-3 bg-white" placeholder="Total Jamaah Laki Laki" id="total_pilgrim_male" name="total_pilgrim_male" value="{{old('total_pilgrim_male')}}">
                </div>
                <div class="mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-gender-female fs-4 text-dark"></i>
                    <input type="number" class="form-control form-control-lg fs-3 bg-white" placeholder="Total Jamaah Perempuan" id="total_pilgrim_female" name="total_pilgrim_female" value="{{old('total_pilgrim_female')}}">
                </div>

                <button type="submit" class="btn shadow-lg mt-3 bg-tanur-green fw-semibold text-white border-0 w-100">Buat & Ajukan Workspace <i class="ti ti-send ms-2"></i></button>
            </div>
        </form>

        <!-- Loading overlay -->
         <div id="loadingOverlay" style="
            display:none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            color: white;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
            padding-top: 30vh;
            user-select: none;
         ">
            <div class="spinner-border text-light" role="status" style="width:2rem; height:2rem; margin-bottom: 1rem;">
               <span class="visually-hidden">Loading...</span>
            </div>
            <div id="loadingText" style="max-width:15em;margin:0px auto;" class="text-center">memuat...</div>
         </div>
    </section>

</div>
@endsection

@section('scripts')
<script src="/assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="/assets/libs/select2/dist/js/select2.min.js"></script>
<script>
    $(function() {
      const loadingOverlay = $('#loadingOverlay');
      const loadingText = $('#loadingText');
      const messages = [
         "membuat dan mengajukan workspace...",
         "mengambil data superior...",
         "membuat log...",
         "memberikan notifikasi..."
      ];
      let currentIndex = 0;
      let intervalId = null;

      $('form').on('submit', function(e) {
         // Tampilkan loading overlay
         loadingOverlay.show();

         // Mulai animasi tulisan loading
         loadingText.text(messages[currentIndex]);
         intervalId = setInterval(() => {
               currentIndex = (currentIndex + 1) % messages.length;
               loadingText.text(messages[currentIndex]);
         }, 2000);

         // jika kamu mau submit ajax, perlu cancel default event
      });
   });
</script>
@endsection