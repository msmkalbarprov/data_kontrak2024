<style>
    .kanan {
        text-align: right
    }
</style>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let status_anggaran = "{{ $status_anggaran }}"
        let kodesubkegiatan = '';

        $('.select_modal').select2({
            dropdownParent: $('#modal_rincian .modal-content'),
            theme: 'bootstrap-5',
            width: '100%'
        });

        $('#rekanan').prop('disabled', true)
        $('#jenis').prop('disabled', true)
        $('#tipe').prop('disabled', true)

        $('.kontrak').hide();
        $('#pesanan').hide();

        let rincian_kontrak = $('#rincian_kontrak').DataTable({
            processing: true,
            searching: true,
            responsive: true,
            ordering: false,
            aoColumns: [{
                    data: 'id',
                    nama: 'id',
                    visible: false
                }, {
                    data: 'kd_sub_kegiatan',
                    nama: 'kd_sub_kegiatan'
                },
                {
                    data: 'kd_rek6',
                    nama: 'kd_rek6'
                },
                {
                    data: 'kd_barang',
                    nama: 'kd_barang'
                },
                {
                    data: 'sumber',
                    nama: 'sumber'
                },
                {
                    data: 'volume',
                    nama: 'volume'
                },
                {
                    data: 'harga',
                    nama: 'harga'
                },
                {
                    data: 'total',
                    nama: 'total'
                },
                {
                    data: 'aksi',
                    nama: 'aksi'
                },
                {
                    data: 'header',
                    nama: 'header',
                    visible: false
                },
                {
                    data: 'sub_header',
                    nama: 'sub_header',
                    visible: false
                },
            ]
        });

        let detail_kontrak = $('#detail_kontrak').DataTable({
            processing: true,
            searching: true,
            responsive: true,
            ordering: false,
            aoColumns: [{
                    data: 'id',
                    nama: 'id',
                    visible: false
                }, {
                    data: 'kd_sub_kegiatan',
                    nama: 'kd_sub_kegiatan',
                    visible: false
                }, {
                    data: 'nm_sub_kegiatan',
                    nama: 'nm_sub_kegiatan',
                },
                {
                    data: 'kd_rek6',
                    nama: 'kd_rek6',
                    visible: false
                },
                {
                    data: 'nm_rek6',
                    nama: 'nm_rek6',
                },
                {
                    data: 'kd_barang',
                    nama: 'kd_barang'
                },
                {
                    data: 'uraian',
                    nama: 'uraian'
                },
                {
                    data: 'sumber',
                    nama: 'sumber',
                    visible: false
                },
                {
                    data: 'nm_sumber',
                    nama: 'nm_sumber'
                },
                {
                    data: 'spesifikasi',
                    nama: 'spesifikasi'
                },
                {
                    data: 'volume1',
                    nama: 'volume1'
                },
                {
                    data: 'volume2',
                    nama: 'volume2'
                },
                {
                    data: 'volume3',
                    nama: 'volume3'
                },
                {
                    data: 'volume4',
                    nama: 'volume4'
                },
                {
                    data: 'satuan1',
                    nama: 'satuan1'
                },
                {
                    data: 'satuan2',
                    nama: 'satuan2'
                },
                {
                    data: 'satuan3',
                    nama: 'satuan3'
                },
                {
                    data: 'satuan4',
                    nama: 'satuan4'
                },
                {
                    data: 'harga',
                    nama: 'harga'
                },
                {
                    data: 'total',
                    nama: 'total'
                },
                {
                    data: 'no_po',
                    nama: 'no_po',
                    visible: false
                },
                {
                    data: 'header',
                    nama: 'header',
                    visible: false
                },
                {
                    data: 'sub_header',
                    nama: 'sub_header',
                    visible: false
                },
                {
                    data: 'aksi',
                    nama: 'aksi'
                }
            ]
        });

        $('#kontrak_awal').on('select2:select', function() {
            let pekerjaan = $(this).find(':selected').data('pekerjaan');
            // let rekanan = $(this).find(':selected').data('rekanan');
            // let pimpinan = $(this).find(':selected').data('pimpinan');
            let kodeskpd = $(this).find(':selected').data('kodeskpd');
            let idkontrak = $(this).find(':selected').data('idkontrak');
            let jns_ang = $(this).find(':selected').data('jns_ang');
            let jenisspp = $(this).find(':selected').data('jenisspp');
            let tipe = $(this).find(':selected').data('tipe');
            let pihakketiga = $(this).find(':selected').data('pihakketiga');
            let namaperusahaan = $(this).find(':selected').data('namaperusahaan');
            let alamatperusahaan = $(this).find(':selected').data('alamatperusahaan');
            let tanggalawal = $(this).find(':selected').data('tanggalawal');
            let tanggalakhir = $(this).find(':selected').data('tanggalakhir');
            let ketentuansanksi = $(this).find(':selected').data('ketentuansanksi');

            let tipeAnggaran = '';

            $.ajax({
                url: "{{ route('daftarAnggaran') }}",
                type: "POST",
                async: false,
                data: {
                    jns_ang: jns_ang
                },
                success: function(data) {
                    tipeAnggaran = data
                },
                error: function(data) {
                    let errors = data.responseJSON
                },
            })

            if (tipeAnggaran == 1) {
                swalAlert(
                    'Tidak dapat membuat kontrak adendum dengan kontrak ini, status anggaran masih ' +
                    jns_ang, 'Silahkan sahkan anggaran selanjutnya');
                $('#kontrak_awal').val(null).change()
                return
            }

            $('#nm_kerja').val(pekerjaan);
            // $('#rekanan').val(rekanan).change();
            // $('#pimpinan').val(pimpinan);
            $('#id_kontrak').val(idkontrak);
            $('#jenis').val(jenisspp).change();
            $('#tipe').val(tipe).change();

            $('#pihak_ketiga').val(pihakketiga);
            $('#nama_perusahaan').val(namaperusahaan);
            $('#alamat_perusahaan').val(alamatperusahaan);
            $('#tanggal_awal').val(tanggalawal);
            $('#tanggal_akhir').val(tanggalakhir);
            $('#sanksi').val(ketentuansanksi);

            if (tipe == 1) {
                $('.kontrak').show();
                $('#pesanan').show();
            } else {
                $('.kontrak').hide();
                $('#pesanan').show();

                $('#no_kontrak').val(null);
            }

            // $('#no_rekening').val($('#rekanan').find(':selected').data('rekening'));
            // $('#npwp').val($('#rekanan').find(':selected').data('npwp'));
            // $('#bank').val($('#rekanan').find(':selected').data('bank'));
            // $('#nm_bank').val($('#rekanan').find(':selected').data('nm_bank'));

            $.ajax({
                url: "{{ route('detail_kontrak') }}",
                type: "POST",
                data: {
                    kontrak_awal: this.value,
                    id_kontrak: idkontrak,
                    kd_skpd: kodeskpd,
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    tampilkanDetailKontrak(data);
                },
                error: function(data) {
                    let errors = data.responseJSON
                },
                complete: function() {
                    $("#overlay").fadeOut(100);
                }
            })
        });

        $('#rekanan').on('select2:select', function() {
            $('#no_rekening').val(null)
            $('#bank').val(null)
            $('#nm_bank').val(null)
            $('#npwp').val(null)

            let rekanan = this.value
            let rekening = $(this).find(':selected').data('rekening');
            let bank = $(this).find(':selected').data('bank');
            let nm_bank = $(this).find(':selected').data('nm_bank');
            let npwp = $(this).find(':selected').data('npwp');

            if (!rekanan) {
                swalAlert('Rekanan tidak boleh kosong');
                return
            }

            if (!rekening) {
                swalAlert('Rekening tidak boleh kosong');
                $('#rekanan').val(null).change()
                return
            }

            if (!bank) {
                swalAlert('Bank tidak boleh kosong');
                $('#rekanan').val(null).change()
                return
            }

            if (!nm_bank) {
                swalAlert('Nama bank tidak boleh kosong');
                $('#rekanan').val(null).change()
                return
            }

            if (!npwp) {
                swalAlert('NPWP tidak boleh kosong');
                $('#rekanan').val(null).change()
                return
            }

            $('#rekanan').val(rekanan)
            $('#no_rekening').val(rekening)
            $('#bank').val(bank)
            $('#nm_bank').val(nm_bank)
            $('#npwp').val(npwp)
        });

        $('#tambah_rincian').on('click', function() {
            // load_kegiatan()
            let kontrak_awal = $('#kontrak_awal').val();

            if (!kontrak_awal) {
                swalAlert('Silahkan pilih kontrak awal sebelum tambah rincian!');
                return
            }

            tipeEdit = '';

            $('#modal_rincian').modal('show')
        })

        $('#kd_sub_kegiatan').on('select2:select', function() {
            load_rekening();
        });

        $('#kd_rek6').on('select2:select', function() {
            load_barang();
        });

        $('#kd_barang').on('select2:select', function() {
            let header = $(this).find(':selected').data('header');
            let sub_header = $(this).find(':selected').data('sub_header');

            load_sumber(header, sub_header);
        });

        $('#sumber').on('select2:select', function() {
            let volume1 = $(this).find(':selected').data('volume1');
            let volume2 = $(this).find(':selected').data('volume2');
            let volume3 = $(this).find(':selected').data('volume3');
            let volume4 = $(this).find(':selected').data('volume4');

            $('#volume1').val(conversi(volume1));
            $('#volume2').val(conversi(volume2));
            $('#volume3').val(conversi(volume3));
            $('#volume4').val(conversi(volume4));

            let satuan1 = $(this).find(':selected').data('satuan1');
            let satuan2 = $(this).find(':selected').data('satuan2');
            let satuan3 = $(this).find(':selected').data('satuan3');
            let satuan4 = $(this).find(':selected').data('satuan4');

            $('#satuan1').val(satuan1);
            $('#satuan2').val(satuan2);
            $('#satuan3').val(satuan3);
            $('#satuan4').val(satuan4);

            $('#volume').val(conversi(parseFloat(volume1) * parseFloat(volume2) * parseFloat(
                volume3) * parseFloat(volume4)));
            $('#harga').val(conversi($(this).find(':selected').data('harga')));
            $('#total').val(conversi($(this).find(':selected').data('total')));
        });

        $('#simpan_rincian').on('click', function() {
            let kd_sub_kegiatan = $('#kd_sub_kegiatan').val();
            let nm_sub_kegiatan = $('#kd_sub_kegiatan').find(':selected').data('nama');

            let kd_rek6 = $('#kd_rek6').val();
            let nm_rek6 = $('#kd_rek6').find(':selected').data('nama');

            let kd_barang = $('#kd_barang').val();
            let header = $('#kd_barang').find(':selected').data('header');
            let sub_header = $('#kd_barang').find(':selected').data('sub_header');

            let sumber = $('#sumber').val();
            let nm_sumber = $('#sumber').find(':selected').data('nama');
            let satuan1 = $('#sumber').find(':selected').data('satuan1');
            let satuan2 = $('#sumber').find(':selected').data('satuan2');
            let satuan3 = $('#sumber').find(':selected').data('satuan3');
            let satuan4 = $('#sumber').find(':selected').data('satuan4');
            let harga = parseFloat($('#sumber').find(':selected').data('harga'));
            let id_po = $('#sumber').find(':selected').data('id');
            let no_po = $('#sumber').find(':selected').data('no_po');
            let uraian = $('#sumber').find(':selected').data('uraian');
            let spesifikasi = $('#sumber').find(':selected').data('spesifikasi');

            let volume1 = parseFloat($('#sumber').find(':selected').data('volume1'));
            let volume2 = parseFloat($('#sumber').find(':selected').data('volume2'));
            let volume3 = parseFloat($('#sumber').find(':selected').data('volume3'));
            let volume4 = parseFloat($('#sumber').find(':selected').data('volume4'));

            let input_volume1 = angka($('#input_volume1').val());
            let input_volume2 = angka($('#input_volume2').val());
            let input_volume3 = angka($('#input_volume3').val());
            let input_volume4 = angka($('#input_volume4').val());

            if (!kd_sub_kegiatan) {
                swalAlert('Kegiatan tidak boleh kosong!');
                return;
            }

            if (!kd_rek6) {
                swalAlert('Kode rekening tidak boleh kosong!');
                return;
            }

            if (!kd_barang) {
                swalAlert('Kode barang tidak boleh kosong!');
                return;
            }

            if (!sumber) {
                swalAlert('Sumber tidak boleh kosong!');
                return;
            }

            if (!harga) {
                swalAlert('Harga tidak boleh kosong!');
                return;
            }

            if (!total) {
                swalAlert('Total anggaran tidak boleh kosong!');
                return;
            }

            if (!input_volume1 || input_volume1 == 0) {
                swalAlert('Input volume 1 tidak boleh kosong!');
                return;
            }

            if (volume1 < input_volume1) {
                swalAlert('Input volume 1 melebihi volume 1');
                return;
            }

            if (volume2 < input_volume2) {
                swalAlert('Input volume 2 melebihi volume 2');
                return;
            }

            if (volume3 < input_volume3) {
                swalAlert('Input volume 3 melebihi volume 3');
                return;
            }

            if (volume3 < input_volume4) {
                swalAlert('Input volume 4 melebihi volume 4');
                return;
            }

            let tampungan = detail_kontrak.rows().data().toArray().map((value) => {
                let result = {
                    id_po: value.id,
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    kd_rek6: value.kd_rek6,
                    kd_barang: value.kd_barang,
                    sumber: value.sumber,
                    header: value.header,
                    sub_header: value.sub_header,
                };
                return result;
            });

            let kondisi = tampungan.map(function(data) {
                if (data.kd_sub_kegiatan.trim() != kd_sub_kegiatan.trim()) {
                    return '1';
                } else if (data.id_po == id_po) {
                    return '2';
                } else if (data.kd_rek6.trim() == kd_rek6.trim() && data.kd_barang.trim() ==
                    kd_barang.trim() && data
                    .sumber.trim() == sumber.trim()) {
                    return '3';
                }
            });

            if (tipeEdit != 'editVolume') {
                if (kondisi.includes("1")) {
                    swalAlert('Kegiatan tidak boleh berbeda dalam 1 kontrak')
                    return;
                }

                if (kondisi.includes("2")) {
                    swalAlert(
                        'Kegiatan, Rekening, Kode Barang dan Sumber Dana telah ada di rincian kontrak'
                    )
                    return;
                }

                if (kondisi.includes("3")) {
                    swalAlert('Sumber tidak boleh sama dalam 1 kode barang')
                    return;
                }
            }

            let data = {
                kd_sub_kegiatan,
                nm_sub_kegiatan,
                kd_rek6,
                nm_rek6,
                kd_barang,
                header,
                sub_header,
                sumber,
                nm_sumber,
                satuan1,
                satuan2,
                satuan3,
                satuan4,
                harga,
                id_po,
                no_po,
                uraian,
                spesifikasi,
                input_volume1,
                input_volume2,
                input_volume3,
                input_volume4,
            };

            Swal.fire({
                title: "Apakah anda yakin?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, simpan!",
                cancelButtonText: "Batal!"
            }).then((result) => {
                if (result.isConfirmed) {
                    simpanRincian(data)
                    bersihkan();
                }
            });
        });

        $('#simpan').on('click', function() {
            let jenis = $('#jenis').val();
            let id_kontrak = $('#id_kontrak').val();
            let no_kontrak = $('#no_kontrak').val();
            let tgl_kontrak = $('#tgl_kontrak').val();
            let kd_skpd = $('#kd_skpd').val();
            let nm_kerja = $('#nm_kerja').val();
            let kontrak_awal = $('#kontrak_awal').val();
            let no_pesanan = $('#no_pesanan').val();
            // let rekanan = $('#rekanan').val();
            // let rekening = $('#rekanan').find(':selected').data('rekening');
            // let bank = $('#rekanan').find(':selected').data('bank');
            // let nm_bank = $('#rekanan').find(':selected').data('nm_bank');
            // let npwp = $('#rekanan').find(':selected').data('npwp');

            // let pimpinan = $('#pimpinan').val();

            let tipe = $('#tipe').val();

            let pihak_ketiga = $('#pihak_ketiga').val();
            let nama_perusahaan = $('#nama_perusahaan').val();
            let alamat_perusahaan = $('#alamat_perusahaan').val();

            let tanggal_awal = $('#tanggal_awal').val();
            let tanggal_akhir = $('#tanggal_akhir').val();
            let sanksi = $('#sanksi').val();


            let total_rincian_kontrak = rupiah($('#total_rincian_kontrak').val());

            let tahun_anggaran = "{{ $tahun }}";
            let tahun_input = tgl_kontrak.substring(0, 4);

            if (!id_kontrak) {
                swalAlert('ID Kontrak harus diisi');
                return
            }

            // if (!no_kontrak) {
            //     swalAlert('No Kontrak harus diisi');
            //     return
            // }

            if (!kontrak_awal) {
                swalAlert('Nomor Kontrak Awal harus diisi');
                return
            }

            if (!tgl_kontrak) {
                swalAlert('Tanggal Kontrak harus diisi');
                return;
            }

            if (tahun_input != tahun_anggaran) {
                swalAlert('Tahun tidak sama dengan tahun anggaran!');
                return
            }

            if (!kd_skpd) {
                swalAlert('Kode SKPD harus diisi');
                return
            }

            if (!nm_kerja) {
                swalAlert('Nama pekerjaan harus diisi');
                return
            }

            // if (!rekanan) {
            //     swalAlert('Rekanan harus diisi');
            //     return
            // }

            // if (!rekening) {
            //     swalAlert('Rekening harus diisi');
            //     return
            // }

            // if (!bank) {
            //     swalAlert('Bank harus diisi');
            //     return
            // }

            // if (!npwp) {
            //     swalAlert('NPWP harus diisi');
            //     return
            // }

            // if (!pimpinan) {
            //     swalAlert('Pimpinan harus diisi');
            //     return
            // }

            if (total_rincian_kontrak == 0) {
                swalAlert('Total Rincian Kontrak tidak boleh NOL')
                return
            }

            if (!status_anggaran) {
                swalAlert('Status Anggaran tidak ada!');
                return
            }

            if (!tipe) {
                swalAlert('Tipe harus dipilih');
                return
            }

            if ((tipe == 1 && !no_kontrak) || (tipe == 1 && !no_pesanan)) {
                swalAlert('Jika pilih kontrak, wajib isi nomor kontrak dan nomor pesanan')
                return
            }

            if (tipe == 2 && !no_pesanan) {
                swalAlert('Jika pilih pesanan, wajib isi nomor pesanan')
                return
            }

            if (!pihak_ketiga) {
                swalAlert('Pihak ketiga harus diisi');
                return
            }

            if (!nama_perusahaan) {
                swalAlert('Nama perusahaan harus diisi');
                return
            }

            if (!alamat_perusahaan) {
                swalAlert('Alamat perusahaan harus diisi');
                return
            }

            if (alamat_perusahaan.length > 1000) {
                swalAlert('Alamat perusahaan tidak boleh lebih dari 1000 karakter');
                return
            }

            if (!tanggal_awal && !tanggal_akhir) {
                swalAlert('Tanggal awal dan tanggal akhir harus diisi');
                return
            }

            if (tanggal_awal > tanggal_akhir) {
                swalAlert('Tanggal Awal tidak boleh lebih besar dari tanggal akhir');
                return
            }

            if (!sanksi) {
                swalAlert('Ketentuan sanksi harus diisi');
                return
            }

            if (sanksi.length > 1000) {
                swalAlert('Ketentuan sanksi tidak boleh lebih dari 1000 karakter');
                return
            }

            let kontrak1 = detail_kontrak.rows().data().toArray().map((value) => {
                let data = {
                    id: value.id,
                    kd_sub_kegiatan: value.kd_sub_kegiatan,
                    nm_sub_kegiatan: value.nm_sub_kegiatan,
                    kd_rek6: value.kd_rek6,
                    nm_rek6: value.nm_rek6,
                    kd_barang: value.kd_barang,
                    uraian: value.uraian,
                    sumber: value.sumber,
                    nm_sumber: value.nm_sumber,
                    spesifikasi: value.spesifikasi,
                    volume1: rupiah(value.volume1),
                    volume2: rupiah(value.volume2),
                    volume3: rupiah(value.volume3),
                    volume4: rupiah(value.volume4),
                    satuan1: value.satuan1,
                    satuan2: value.satuan2,
                    satuan3: value.satuan3,
                    satuan4: value.satuan4,
                    harga: rupiah(value.harga),
                    total: rupiah(value.total),
                    no_po: value.no_po,
                    header: value.header,
                    sub_header: value.sub_header,
                };
                return data;
            });

            let total_kontrak = kontrak1.reduce((prev, current) => (prev += parseFloat(current.total)),
                0);

            if (kontrak1.length == 0) {
                swalAlert('Rincian Rekening tidak boleh kosong!');
                return;
            }

            if (total_rincian_kontrak != total_kontrak) {
                swalAlert('Total detail kontrak tidak sesuai dengan total di dalam rincian kontrak!')
                return
            }

            if (!jenis) {
                swalAlert('Jenis harus dipilih!');
                return
            }

            if (jenis == 1 && total_rincian_kontrak > 15000000) {
                swalAlert('Kontrak tidak boleh melebihi 15 juta, jika UP/GU!');
                return
            }

            let kontrak = JSON.stringify(kontrak1);

            let data = {
                id_kontrak,
                no_kontrak,
                tgl_kontrak,
                kd_skpd,
                nm_kerja,
                // rekanan,
                // rekening,
                // bank,
                // npwp,
                // pimpinan,
                total_rincian_kontrak,
                kontrak,
                kontrak_awal,
                status_anggaran,
                jenis,
                jenis,
                tipe,
                no_pesanan,
                pihak_ketiga,
                nama_perusahaan,
                alamat_perusahaan,
                tanggal_awal,
                tanggal_akhir,
                sanksi
            };

            Swal.fire({
                title: "Apakah anda yakin " + "dengan status anggaran " + status_anggaran + "?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, simpan!",
                cancelButtonText: "Batal!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('kontrak_adendum.store') }}",
                        type: "POST",
                        dataType: 'json',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            data: data
                        },
                        beforeSend: function() {
                            $('#simpan_rincian').prop('disabled', true);
                            $("#overlay").fadeIn(100);
                        },
                        success: function(data) {
                            Swal.fire({
                                title: "Berhasil!",
                                text: data.message,
                                icon: "success"
                            });
                            window.location.href =
                                "{{ route('kontrak_adendum.index') }}"
                        },
                        error: function(data) {
                            $('#simpan_rincian').prop('disabled', false);
                            $("#overlay").fadeOut(100);
                            let errors = data.responseJSON;

                            Swal.fire({
                                title: "Error!",
                                html: errors.error,
                                icon: "error"
                            });
                        },
                        complete: function(data) {
                            $('#simpan_rincian').prop('disabled', false);
                            $("#overlay").fadeOut(100);
                        }
                    })
                }
            });
        });

        function load_kegiatan(kd_sub_kegiatan) {
            bersihkan('kegiatan');

            $.ajax({
                url: "{{ route('kode_sub_kegiatan') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                    tipe: 'adendum',
                    kd_sub_kegiatan: kd_sub_kegiatan,
                    status_anggaran: status_anggaran
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    $('#kd_sub_kegiatan').empty();
                    $('#kd_sub_kegiatan').append(
                        `<option value="" disabled selected>Silahkan pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_sub_kegiatan').append(
                            `<option value="${data.kd_sub_kegiatan}" data-nama="${data.nm_sub_kegiatan}">${data.kd_sub_kegiatan} | ${data.nm_sub_kegiatan}</option>`
                        );
                    })
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            })
        }

        function load_rekening() {
            bersihkan('rekening');

            $.ajax({
                url: "{{ route('rekening') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: $('#kd_sub_kegiatan').val(),
                    "_token": "{{ csrf_token() }}",
                    status_anggaran: status_anggaran
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    $('#kd_rek6').empty();
                    $('#kd_rek6').append(
                        `<option value="" disabled selected>Silahkan pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_rek6').append(
                            `<option value="${data.kd_rek6}" data-nama="${data.nm_rek6}">${data.kd_rek6} | ${data.nm_rek6}</option>`
                        );
                    })
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            });
        }

        function load_barang() {
            bersihkan('barang');

            $.ajax({
                url: "{{ route('kode_barang') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: $('#kd_sub_kegiatan').val(),
                    kd_rek6: $('#kd_rek6').val(),
                    "_token": "{{ csrf_token() }}",
                    status_anggaran: status_anggaran
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    $('#kd_barang').empty();
                    $('#kd_barang').append(
                        `<option value="" disabled selected>Silahkan pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#kd_barang').append(
                            `<option value="${data.kd_barang}" data-header="${data.header}" data-sub_header="${data.sub_header}">${data.kd_barang} | ${data.uraian} | ${data.header} | ${data.sub_header}</option>`
                        );
                    })
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            });
        }

        function load_sumber(header, sub_header) {
            bersihkan('sumber');

            $.ajax({
                url: "{{ route('sumber_dana') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: $('#kd_sub_kegiatan').val(),
                    kd_rek6: $('#kd_rek6').val(),
                    kd_barang: $('#kd_barang').val(),
                    header: header,
                    sub_header: sub_header,
                    "_token": "{{ csrf_token() }}",
                    status_anggaran: status_anggaran
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    $('#sumber').empty();
                    $('#sumber').append(
                        `<option value="" disabled selected>Silahkan pilih</option>`);
                    $.each(data, function(index, data) {
                        $('#sumber').append(
                            `<option value="${data.sumber}" data-nama="${data.nm_sumber}" data-volume1="${data.volume1}" data-volume2="${data.volume2}" data-volume3="${data.volume3}" data-volume4="${data.volume4}" data-satuan1="${data.satuan1}" data-satuan2="${data.satuan2}" data-satuan3="${data.satuan3}" data-satuan4="${data.satuan4}" data-harga="${data.harga}" data-total="${data.total}" data-id="${data.id}" data-no_po="${data.no_po}" data-uraian="${data.uraian}" data-spesifikasi="${data.spesifikasi}">${data.sumber} | ${data.nm_sumber}</option>`
                        );
                    })
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            });
        }

        function simpanRincian(data) {
            if (tipeEdit == 'editVolume') {
                rincian_kontrak.rows(function(idx, item, node) {
                    return trim(item.kd_sub_kegiatan) == trim(data.kd_sub_kegiatan) && trim(item
                            .kd_rek6) == trim(data.kd_rek6) && trim(item.header) == trim(data.header) &&
                        trim(item.sub_header) == trim(data.sub_header) && trim(item.sumber) == trim(data
                            .sumber) && trim(item.kd_barang) == trim(data.kd_barang)
                }).remove().draw();

                detail_kontrak.rows(function(idx, item, node) {
                    return trim(item.kd_sub_kegiatan) == trim(data.kd_sub_kegiatan) && trim(item
                            .kd_rek6) == trim(data.kd_rek6) && trim(item.header) == trim(data.header) &&
                        trim(item.sub_header) == trim(data.sub_header) && trim(item.sumber) == trim(data
                            .sumber) && trim(item.kd_barang) == trim(data.kd_barang)
                }).remove().draw();
            }


            let cek = [data.input_volume1, data.input_volume2, data.input_volume3, data.input_volume4];

            let volume = cek.reduce((prev, current) => {
                if (current != 0) {
                    prev *= current
                }
                return prev
            });

            let total = volume * data.harga;

            rincian_kontrak.row.add({
                'id': data.id_po,
                'kd_sub_kegiatan': data.kd_sub_kegiatan,
                'kd_rek6': data.kd_rek6,
                'kd_barang': data.kd_barang,
                'sumber': data.sumber,
                'volume': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(volume),
                'harga': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(data.harga),
                'total': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(total),
                'aksi': tipeEdit == 'editVolume' ?
                    `<a href="javascript:void(0);" onclick="editRincian('${data.id_po}','${data.kd_sub_kegiatan}','${data.kd_rek6}','${data.kd_barang}','${data.sumber}','${data.header}','${data.sub_header}','${data.input_volume1}','${data.input_volume2}','${data.input_volume3}','${data.input_volume4}','editVolume')" class="btn btn-warning btn-sm"><i class="fadeIn animated bx bx-edit"></i></a>` :
                    `<a href="javascript:void(0);" onclick="hapusRincian('${data.id_po}','${total}')" class="btn btn-danger btn-sm"><i class="fadeIn animated bx bx-trash"></i></a>`,
                'header': data.header,
                'sub_header': data.sub_header,
            }).draw();

            detail_kontrak.row.add({
                'id': data.id_po,
                'kd_sub_kegiatan': data.kd_sub_kegiatan,
                'nm_sub_kegiatan': data.nm_sub_kegiatan,
                'kd_rek6': data.kd_rek6,
                'nm_rek6': data.nm_rek6,
                'kd_barang': data.kd_barang,
                'uraian': data.uraian,
                'sumber': data.sumber,
                'nm_sumber': data.nm_sumber,
                'spesifikasi': data.spesifikasi,
                'volume1': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(data.input_volume1),
                'volume2': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(data.input_volume2),
                'volume3': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(data.input_volume3),
                'volume4': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(data.input_volume4),
                'satuan1': data.satuan1,
                'satuan2': data.satuan2,
                'satuan3': data.satuan3,
                'satuan4': data.satuan4,
                'harga': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(data.harga),
                'total': new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(total),
                'no_po': data.no_po,
                'header': data.header,
                'sub_header': data.sub_header,
                'aksi': tipeEdit == 'editVolume' ?
                    `<a href="javascript:void(0);" onclick="editRincian('${data.id_po}','${data.kd_sub_kegiatan}','${data.kd_rek6}','${data.kd_barang}','${data.sumber}','${data.header}','${data.sub_header}','${data.input_volume1}','${data.input_volume2}','${data.input_volume3}','${data.input_volume4}','editVolume')" class="btn btn-warning btn-sm"><i class="fadeIn animated bx bx-edit"></i></a>` :
                    `<a href="javascript:void(0);" onclick="hapusRincian('${data.id_po}','${total}')" class="btn btn-danger btn-sm"><i class="fadeIn animated bx bx-trash"></i></a>`,
            }).draw();

            let total_kontrak = rincian_kontrak.rows().data().toArray().reduce((prev, current) => (prev +=
                    rupiah(current.total)),
                0);

            $('#total_detail_kontrak').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_kontrak));

            $('#total_rincian_kontrak').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_kontrak));

            $('#modal_rincian').modal('hide')

            kodesubkegiatan = data.kd_sub_kegiatan

            load_kegiatan(kodesubkegiatan)
        }

        function tampilkanDetailKontrak(item) {
            rincian_kontrak.clear().draw();
            detail_kontrak.clear().draw();

            let total_detail_kontrak = rupiah($('#total_detail_kontrak').val());
            let total_rincian_kontrak = rupiah($('#total_rincian_kontrak').val());

            $.each(item, function(index, data) {
                let cek = [data.volume1, data.volume2, data.volume3, data.volume4];

                let volume = cek.reduce((prev, current) => {
                    if (current != 0) {
                        prev *= current
                    }
                    return prev
                });

                let total = volume * data.harga;

                rincian_kontrak.row.add({
                    'id': data.idtrdpo,
                    'kd_sub_kegiatan': data.kodesubkegiatan,
                    'kd_rek6': data.kodeakun,
                    'kd_barang': data.kodebarang,
                    'sumber': data.kodesumberdana,
                    'volume': new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(volume),
                    'harga': new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.harga),
                    'total': new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(total),
                    'aksi': `<a href="javascript:void(0);" onclick="editRincian('${data.id_po}','${data.kodesubkegiatan}','${data.kodeakun}','${data.kodebarang}','${data.kodesumberdana}','${data.header}','${data.subheader}','${data.volume1}','${data.volume2}','${data.volume3}','${data.volume4}','editVolume')" class="btn btn-warning btn-sm"><i class="fadeIn animated bx bx-edit"></i></a>`,
                    'header': data.header,
                    'sub_header': data.subheader,
                }).draw();

                detail_kontrak.row.add({
                    'id': data.idtrdpo,
                    'kd_sub_kegiatan': data.kodesubkegiatan,
                    'nm_sub_kegiatan': data.namasubkegiatan,
                    'kd_rek6': data.kodeakun,
                    'nm_rek6': data.namaakun,
                    'kd_barang': data.kodebarang,
                    'uraian': data.uraianbarang,
                    'sumber': data.kodesumberdana,
                    'nm_sumber': data.namasumberdana,
                    'spesifikasi': data.spek,
                    'volume1': new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.volume1),
                    'volume2': new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.volume2),
                    'volume3': new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.volume3),
                    'volume4': new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.volume4),
                    'satuan1': data.satuan1,
                    'satuan2': data.satuan2,
                    'satuan3': data.satuan3,
                    'satuan4': data.satuan4,
                    'harga': new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(data.harga),
                    'total': new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(total),
                    'no_po': data.nomorpo,
                    'header': data.header,
                    'sub_header': data.subheader,
                    'aksi': `<a href="javascript:void(0);" onclick="editRincian('${data.id_po}','${data.kodesubkegiatan}','${data.kodeakun}','${data.kodebarang}','${data.kodesumberdana}','${data.header}','${data.subheader}','${data.volume1}','${data.volume2}','${data.volume3}','${data.volume4}','editVolume')" class="btn btn-warning btn-sm"><i class="fadeIn animated bx bx-edit"></i></a>`,
                }).draw();
                total_rincian_kontrak += parseFloat(total)
                total_detail_kontrak += parseFloat(total)

                kodesubkegiatan = data.kodesubkegiatan
            })

            $('#total_detail_kontrak').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_detail_kontrak));

            $('#total_rincian_kontrak').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_rincian_kontrak));

            load_kegiatan(kodesubkegiatan)
        }
    });

    let tipeEdit = ''

    function bersihkan(tipe) {
        if (tipe == 'kegiatan') {
            $('#kd_rek6').empty();
            $('#kd_barang').empty();
            $('#sumber').empty();
        } else if (tipe == 'rekening') {
            $('#kd_barang').empty();
            $('#sumber').empty();
        } else if (tipe == 'barang') {
            $('#sumber').empty();
        } else if (tipe == 'sumber') {} else {
            $('#kd_rek6').val(null).change();
            $('#kd_barang').empty();
            $('#sumber').empty();
        }

        $('#volume1').val(null)
        $('#volume2').val(null)
        $('#volume3').val(null)
        $('#volume4').val(null)

        $('#volume').val(null)

        $('#satuan1').val(null)
        $('#satuan2').val(null)
        $('#satuan3').val(null)
        $('#satuan4').val(null)

        $('#input_volume1').val(null)
        $('#input_volume2').val(null)
        $('#input_volume3').val(null)
        $('#input_volume4').val(null)

        $('#harga').val(null)
        $('#total').val(null)
    }

    function hapusRincian(id, total) {
        let rincian_kontrak = $('#rincian_kontrak').DataTable();
        let detail_kontrak = $('#detail_kontrak').DataTable();

        let total_detail_kontrak = rupiah($('#total_detail_kontrak').val());
        let total_rincian_kontrak = rupiah($('#total_rincian_kontrak').val());

        Swal.fire({
            title: "Apakah anda yakin?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal!"
        }).then((result) => {
            if (result.isConfirmed) {
                rincian_kontrak.rows(function(idx, data, node) {
                    return data.id == id
                }).remove().draw();
                detail_kontrak.rows(function(idx, data, node) {
                    return data.id == id
                }).remove().draw();

                $('#total_detail_kontrak').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(total_detail_kontrak - parseFloat(total)));

                $('#total_rincian_kontrak').val(new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2
                }).format(total_rincian_kontrak - parseFloat(total)));

                Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: "Data berhasil dihapus!",
                });
            }
        });


    }

    function loadEdit(kd_sub_kegiatan, kd_rek6, kd_barang, sumber, header, sub_header, volume1, volume2, volume3,
        volume4) {
        $.ajax({
            url: "{{ route('data_adendum') }}",
            type: "POST",
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                tipe: 'adendum',
                kd_sub_kegiatan: kd_sub_kegiatan,
                kd_rek6: kd_rek6,
                kd_barang: kd_barang,
                sumber: sumber,
                header: header,
                sub_header: sub_header,
                status_anggaran: "{{ $status_anggaran }}"
            },
            beforeSend: function() {
                $("#overlay").fadeIn(100);
            },
            success: function(data) {
                $('#kd_sub_kegiatan').empty();
                $('#kd_sub_kegiatan').append(
                    `<option value="" disabled selected>Silahkan pilih</option>`);
                $.each(data.kegiatan, function(index, kegiatan) {
                    if (kegiatan.kd_sub_kegiatan == kd_sub_kegiatan) {
                        $('#kd_sub_kegiatan').append(
                            `<option value="${kegiatan.kd_sub_kegiatan}" data-nama="${kegiatan.nm_sub_kegiatan}" selected>${kegiatan.kd_sub_kegiatan} | ${kegiatan.nm_sub_kegiatan}</option>`
                        );
                    }
                });

                $('#kd_rek6').empty();
                $('#kd_rek6').append(
                    `<option value="" disabled selected>Silahkan pilih</option>`);
                $.each(data.rekening, function(index, rekening) {
                    if (rekening.kd_rek6 == kd_rek6) {
                        $('#kd_rek6').append(
                            `<option value="${rekening.kd_rek6}" data-nama="${rekening.nm_rek6}" selected>${rekening.kd_rek6} | ${rekening.nm_rek6}</option>`
                        );
                    }

                });

                $('#kd_barang').empty();
                $('#kd_barang').append(
                    `<option value="" disabled selected>Silahkan pilih</option>`);
                $.each(data.kodeBarang, function(index, barang) {
                    if (barang.kd_barang == kd_barang && barang.header == header && barang
                        .sub_header == sub_header) {
                        $('#kd_barang').append(
                            `<option value="${barang.kd_barang}" data-header="${barang.header}" data-sub_header="${barang.sub_header}" selected>${barang.kd_barang} | ${barang.uraian} | ${barang.header} | ${barang.sub_header}</option>`
                        );
                    }
                });

                $('#sumber').empty();
                $('#sumber').append(
                    `<option value="" disabled selected>Silahkan pilih</option>`);
                $.each(data.sumber, function(index, sumber_dana) {
                    if (sumber_dana.sumber == sumber) {
                        $('#sumber').append(
                            `<option value="${sumber_dana.sumber}" data-nama="${sumber_dana.nm_sumber}" data-volume1="${sumber_dana.volume1}" data-volume2="${sumber_dana.volume2}" data-volume3="${sumber_dana.volume3}" data-volume4="${sumber_dana.volume4}" data-satuan1="${sumber_dana.satuan1}" data-satuan2="${sumber_dana.satuan2}" data-satuan3="${sumber_dana.satuan3}" data-satuan4="${sumber_dana.satuan4}" data-harga="${sumber_dana.harga}" data-total="${sumber_dana.total}" data-id="${sumber_dana.id}" data-no_po="${sumber_dana.no_po}" data-uraian="${sumber_dana.uraian}" data-spesifikasi="${sumber_dana.spesifikasi}" selected>${sumber_dana.sumber} | ${sumber_dana.nm_sumber}</option>`
                        );

                        $('#volume1').val(conversi(sumber_dana.volume1));
                        $('#volume2').val(conversi(sumber_dana.volume2));
                        $('#volume3').val(conversi(sumber_dana.volume3));
                        $('#volume4').val(conversi(sumber_dana.volume4));

                        $('#satuan1').val(sumber_dana.satuan1);
                        $('#satuan2').val(sumber_dana.satuan2);
                        $('#satuan3').val(sumber_dana.satuan3);
                        $('#satuan4').val(sumber_dana.satuan4);

                        $('#volume').val(conversi(parseFloat(sumber_dana.volume1) * parseFloat(
                                sumber_dana.volume2) * parseFloat(sumber_dana.volume3) *
                            parseFloat(sumber_dana.volume4)));

                        $('#harga').val(conversi(sumber_dana.harga));

                        $('#total').val(conversi(sumber_dana.total));
                    }
                })
            },
            complete: function(data) {
                $("#overlay").fadeOut(100);
            }
        })
    }

    function editRincian(id, kd_sub_kegiatan, kd_rek6, kd_barang, sumber, header, sub_header, volume1, volume2, volume3,
        volume4, tipe) {
        tipeEdit = tipe
        let rincian_kontrak = $('#rincian_kontrak').DataTable();
        let detail_kontrak = $('#detail_kontrak').DataTable();

        bersihkan();

        $('#input_volume1').val(volume1);
        $('#input_volume2').val(volume2);
        $('#input_volume3').val(volume3);
        $('#input_volume4').val(volume4);

        loadEdit(kd_sub_kegiatan, kd_rek6, kd_barang, sumber, header, sub_header);

        $('#modal_rincian').modal('show')
    }
</script>
