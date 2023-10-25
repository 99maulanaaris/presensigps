@extends('layouts.admin.tabler')
@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->

                <h2 class="page-title">
                    Monitoring Presensi
                </h2>
            </div>

        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-event" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"></path>
                                            <path d="M16 3l0 4"></path>
                                            <path d="M8 3l0 4"></path>
                                            <path d="M4 11l16 0"></path>
                                            <path d="M8 15h2v2h-2z"></path>
                                        </svg>
                                    </span>
                                    <input type="month" id="tanggal" value="{{ date("Y-m-d") }}" name="tanggal" value="" class="form-control" placeholder="Tanggal Presensi" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-3">
                                <select name="kelas" id="kelas" class="form-control">
                                    <option value="">Pilih Kelas</option>
                                    @foreach ($kelas as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <select name="jurusan" id="jurusan" class="form-control">
                                    <option value="">Pilih Jurusan</option>
                                    @foreach ($jurusan as $value)
                                        <option value="{{ $value->id }}">{{ $value->nama_dept }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <div class="btn btn-primary btn-md btn-fillter">
                                    <i class="fa-solid fa-magnifying-glass mr-5"></i>&nbsp;Cari
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table id="dataTable" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Nis</th>
                                            <th>Nama Siswa</th>
                                            <th>Kelas</th>
                                            <th>Jurusan</th>
                                            <th>Jam Masuk</th>
                                            <th>Foto</th>
                                            <th>Jam Pulang</th>
                                            <th>Foto</th>
                                            <th>Keterangan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody align="center">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-blur fade" id="modalInput" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lokasi Presensi User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="map" style="height: 500px;">
            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(document).ready(function(){
        // $("#tanggal").datepicker({
        //     autoclose: true,
        //     todayHighlight: false,
        //     format: 'yyyy-mm-dd'
        // });

        let table = $('#dataTable').DataTable({
            destroy: true,
            order: [[0,'desc']],
            ordering: true,
            bInfo: false,
            bLengthChange: false,
            scrollX: true,
            sScrollXInner: "100%",
            serveside:true,
            language: {
                search: "",
                searchPlaceholder: "Search",
                sLengthMenu: "_MENU_items",
            },
            ajax : {
                url : '{{ route('presensi.index') }}',
                method : 'GET',
                data : function (d){
                    d._token = '{{ csrf_token() }}';
                    d.date =  $("#tanggal").val();
                    d.kelas = $('#kelas').val();
                    d.jurusan = $('#jurusan').val();
                }
            },
            columns : [{
                data : "DT_RowIndex"
            },{
                data : function(data){
                    let nis = data.student.nis || '-';
                    return nis;
                }
            },{
                data : function(data){
                    let nama = data.student.nama || '-';
                    return nama;
                }
            },{
                data : function(data){
                    let kelas = data.student.kelas || '-';
                    kelas = kelas.nama || '-';
                    return kelas;
                }
            },{
                data : function(data){
                    let jurusan = data.student.jurusan || '-';
                    jurusan = jurusan.nama_dept || '-';
                    return jurusan;
                }
            },{
                data : function(data){
                    let jam = data.jam_in || '-';
                    return jam;
                }
            },{
                data: function(data) {
                let photo = data.foto_in || 'not_found.png';
                let image = '{{ asset('/uploads/absensi') }}' + '/' + photo;

                return ` <a href="${image}"
                    data-lightbox=" ${data.student.nama}"
                    data-title=" ${data.student.kelas.nama}">
                    <img src="${image}"
                        class="rounded"
                        onerror="this.onerror=null;this.parentNode.href='{{ asset('assets/img/nophoto.png') }}';this.src='{{ asset('assets/img/nophoto.png') }}';">
                </a> `
                },
                name: 'Foto',
            },{
                data : function(data){
                    let jam = data.jam_out || '-';
                    return jam;
                }
            },{
                data: function(data) {
                let photo = data.foto_out || 'not_found.png';
                let image = '{{ asset('/uploads/absensi') }}' + '/' + photo;

                return ` <a href="${image}"
                    data-lightbox=" ${data.student.nama}"
                    data-title=" ${data.student.kelas.nama}">
                    <img src="${image}"
                        class="rounded"
                        onerror="this.onerror=null;this.parentNode.href='{{ asset('assets/img/nophoto.png') }}';this.src='{{ asset('assets/img/nophoto.png') }}';">
                </a> `
                },
                name: 'Foto',
            },{
                data : function(data){
                   let status = 'TEPAT WAKTU';
                   let jam_masuk = data.jam.akhir_jam_masuk;
                   let jam_siswa = data.jam_in;
                   if(jam_siswa > jam_masuk){
                        return `<span class="badge text-bg-danger">TERLAMBAT</span>`
                   }else{
                        return ` <span class="badge text-bg-success">TEPAT WAKTU</span>`
                   }
                }
            },{
                data : function(data){
                    let id = data.lokasi_in || '-';
                    let nama = data.student.nama || '-';
                    return `<div data-id="${id}" data-nama="${nama}" class="btn btn-success btn-lokasi" id="btn-lokasi">Lihat Lokasi</div>`;
                }
            }]
        });

        table.on('click','.btn-lokasi',function(){

            let lokasi = $(this).data('id');
            let nama = $(this).data('nama');
            var lok = lokasi.split(",");
            var latitude = lok[0];
            var longitude = lok[1];
            $('#modalInput').on('shown.bs.modal', function () {
                var map = L.map('map').setView([latitude, longitude], 18);
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                var marker = L.marker([latitude, longitude]).addTo(map);
                var circle = L.circle([-7.291612116081597, 108.23092644299736], {
                    color: 'red'
                    , fillColor: '#f03'
                    , fillOpacity: 0.5
                    , radius: 20
                }).addTo(map);
                var popup = L.popup()
                    .setLatLng([latitude, longitude])
                    .setContent(nama)
                    .openOn(map);
            });
            $('#modalInput').modal('show');
        })

        $('.btn-fillter').on('click',function(){
            table.ajax.reload();
        });

    })
</script>
@endpush
