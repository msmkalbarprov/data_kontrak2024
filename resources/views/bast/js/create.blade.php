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

        let dataKontrak = [];

        let status_anggaran = "{{ $status_anggaran }}"
        $('#rekanan').prop('disabled', true);

        $('#bap').hide();
        $('#bast').hide();

        $('.select_modal').select2({
            dropdownParent: $('#modal_rincian .modal-content'),
            theme: 'bootstrap-5',
            width: '100%'
        });

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
                }
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

        $('#jenis_kontrak').on('select2:select', function() {
            let kontrak = $('#kontrak').val();

            if (!kontrak) {
                swalAlert("Silahkan pilih nomor kontrak");
                $(this).val(null).change()
                return
            }

            if (this.value == 2) {
                $('#no_bast').val(null);
                $('#tgl_bast').val(null);
                $('#bap').show();
                $('#bast').hide();
            } else {
                $('#no_bap').val(null);
                $('#tgl_bap').val(null);
                $('#bap').hide();
                $('#bast').show();
            }

            isiKeterangan()
        });

        $('#no_bast').on('keyup', function() {
            isiKeterangan();
        })

        $('#no_bap').on('keyup', function() {
            isiKeterangan();
        })

        $('#tgl_bast').on('change', function() {
            isiKeterangan();
        })

        $('#tgl_bap').on('change', function() {
            isiKeterangan();
        })

        $('#kontrak').on('select2:select', function() {
            $('#nm_kerja').val(null);
            // $('#rekanan').val(null).change();
            // $('#pimpinan').val(null);

            // $('#no_rekening').val(null);
            // $('#npwp').val(null);
            // $('#bank').val(null);
            // $('#nm_bank').val(null);

            $('#realisasi_fisik_lalu').val(null);

            $('#total_realisasi_fisik').val(null);

            rincian_kontrak.clear().draw();
            detail_kontrak.clear().draw();

            $('#total_detail_kontrak').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(0));

            $('#total_rincian_kontrak').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(0));

            kontrak = $(this).find(':selected');

            $.ajax({
                url: "{{ route('cek_kontrak') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                    kontrak: this.value
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(response) {
                    $('#nm_kerja').val(kontrak.data('pekerjaan'));
                    // $('#rekanan').val(kontrak.data('rekanan')).change();
                    // $('#pimpinan').val(kontrak.data('pimpinan'));

                    // $('#no_rekening').val($('#rekanan').find(':selected').data('rekening'));
                    // $('#npwp').val($('#rekanan').find(':selected').data('npwp'));
                    // $('#bank').val($('#rekanan').find(':selected').data('bank'));
                    // $('#nm_bank').val($('#rekanan').find(':selected').data('nm_bank'));

                    $('#pihak_ketiga').val(kontrak.data('pihakketiga'));
                    $('#nama_perusahaan').val(kontrak.data('namaperusahaan'));
                    $('#alamat_perusahaan').val(kontrak.data('alamatperusahaan'));
                    $('#tanggal_awal').val(kontrak.data('tanggalawal'));
                    $('#tanggal_akhir').val(kontrak.data('tanggalakhir'));
                    $('#sanksi').val(kontrak.data('ketentuansanksi'));

                    $('#tipe').val(kontrak.data('tipe'));

                    let realisasi_fisik_lalu = parseFloat(kontrak.data(
                        'realisasi_fisik_lalu'))
                    let realisasi_fisik = angka($('#realisasi_fisik').val());

                    $('#realisasi_fisik_lalu').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(realisasi_fisik_lalu));

                    $('#total_realisasi_fisik').val(new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2
                    }).format(realisasi_fisik_lalu + realisasi_fisik));

                    rincian_kontrak.clear().draw();
                    detail_kontrak.clear().draw();

                    isiKeterangan()
                },
                error: function(data) {
                    $("#overlay").fadeOut(100);
                    let errors = data.responseJSON;

                    Swal.fire({
                        title: "Error!",
                        html: errors.error,
                        icon: "error"
                    });

                    $('#kontrak').val(null).change();
                    return;
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            });
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
            let kontrak = $('#kontrak').val();

            if (!kontrak) {
                swalAlert('Silahkan pilih kontrak!');
                return;
            }

            load_kegiatan()
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
            load_realisasi(this.value);
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

            let realisasi_volume1 = rupiah($('#realisasi_volume1').val());
            let realisasi_volume2 = rupiah($('#realisasi_volume2').val());
            let realisasi_volume3 = rupiah($('#realisasi_volume3').val());
            let realisasi_volume4 = rupiah($('#realisasi_volume4').val());

            let input_volume1 = angka($('#input_volume1').val());
            let input_volume2 = angka($('#input_volume2').val());
            let input_volume3 = angka($('#input_volume3').val());
            let input_volume4 = angka($('#input_volume4').val());

            let jns_ang = $('#kontrak').find(':selected').data('jns_ang');

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

            if (!jns_ang) {
                swalAlert('Kontrak tidak boleh kosong!');
                return
            }

            // PROTEKSI SISA REALISASI PER VOLUME
            if (input_volume1 > (volume1 - realisasi_volume1)) {
                swalAlert('Input volume1 melebihi sisa anggaran volume1');
                return
            }

            if (input_volume2 > (volume2 - realisasi_volume2)) {
                swalAlert('Input volume2 melebihi sisa anggaran volume2');
                return
            }

            if (input_volume3 > (volume3 - realisasi_volume3)) {
                swalAlert('Input volume3 melebihi sisa anggaran volume3');
                return
            }

            if (input_volume4 > (volume4 - realisasi_volume4)) {
                swalAlert('Input volume4 melebihi sisa anggaran volume4');
                return
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

            if (kondisi.includes("1")) {
                swalAlert('Kegiatan tidak boleh berbeda dalam 1 BAST')
                return;
            }

            if (kondisi.includes("2")) {
                swalAlert(
                    'Kegiatan, Rekening, Kode Barang dan Sumber Dana telah ada di rincian BAST')
                return;
            }

            if (kondisi.includes("3")) {
                swalAlert('Sumber tidak boleh sama dalam 1 kode barang')
                return;
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
                jns_ang
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
                    $.ajax({
                        url: "{{ route('cek_rincian_bast') }}",
                        type: "POST",
                        dataType: 'json',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            data: data,
                            kontrak: $('#kontrak').val(),
                            id_kontrak: $('#kontrak').find(':selected').data(
                                'id_kontrak'),
                            status_anggaran: status_anggaran
                        },
                        beforeSend: function() {
                            $('#simpan_rincian').prop('disabled', true);
                            $("#overlay").fadeIn(100);
                        },
                        success: function(response) {
                            simpanRincian(data, response)
                            bersihkan()

                            Swal.fire({
                                title: "Berhasil!",
                                text: response.message,
                                icon: "success"
                            });
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
                    });
                }
            });
        });

        $('#simpan').on('click', function() {
            let jenis_kontrak = $('#jenis_kontrak').val();
            // let no_pesanan = $('#no_pesanan').val();
            // let tgl_pesanan = $('#tgl_pesanan').val();
            let no_bast = $('#no_bast').val();
            let tgl_bast = $('#tgl_bast').val();
            let no_bap = $('#no_bap').val();
            let tgl_bap = $('#tgl_bap').val();

            let kd_skpd = $('#kd_skpd').val();
            let nm_skpd = $('#nm_skpd').val();
            let no_kontrak = $('#kontrak').val();
            let keterangan = $('#keterangan').val();
            let id_kontrak = $('#kontrak').find(':selected').data('id_kontrak');
            let anggaran_kontrak = $('#kontrak').find(':selected').data('jns_ang');
            let status_kontrak = $('#status_kontrak').val();

            let total_rincian_kontrak = rupiah($('#total_rincian_kontrak').val());

            let realisasi_fisik_lalu = rupiah($('#realisasi_fisik_lalu').val());
            let realisasi_fisik = angka($('#realisasi_fisik').val());
            let total_realisasi_fisik = rupiah($('#total_realisasi_fisik').val());

            let tahun_anggaran = "{{ $tahun }}";

            let tahun_input_bap = tgl_bap.substring(0, 4);
            let tahun_input_bast = tgl_bast.substring(0, 4);

            if (!jenis_kontrak) {
                swalAlert('Jenis harus dipilih!');
                return
            }

            // if (!no_pesanan) {
            //     swalAlert('No Pesanan harus diisi!');
            //     return
            // }

            // if (!tgl_pesanan) {
            //     swalAlert('Tanggal Pesanan harus diisi!');
            //     return
            // }

            if (jenis_kontrak == 2) {
                if (!no_bap) {
                    swalAlert('No BAP harus diisi!');
                    return
                }

                if (!tgl_bap) {
                    swalAlert('Tanggal BAP harus diisi!');
                    return
                }

                if (tahun_input_bap != tahun_anggaran) {
                    swalAlert('Tahun tidak sama dengan tahun anggaran!');
                    return
                }
            } else {
                if (!no_bast) {
                    swalAlert('No BAST harus diisi!');
                    return
                }

                if (!tgl_bast) {
                    swalAlert('Tanggal BAST harus diisi!');
                    return
                }

                if (tahun_input_bast != tahun_anggaran) {
                    swalAlert('Tahun tidak sama dengan tahun anggaran!');
                    return
                }
            }

            if (!kd_skpd) {
                swalAlert('Kode SKPD harus diisi');
                return
            }

            if (!status_anggaran) {
                swalAlert('Status Anggaran belum ada');
                return
            }

            if (!status_kontrak) {
                swalAlert('Status harus dipilih!');
                return
            }

            if (!keterangan) {
                swalAlert('Keterangan harus diisi!');
                return;
            }

            if (keterangan.length > 1000) {
                swalAlert('Keterangan tidak boleh lebih dari 1000 karakter!');
                return;
            }

            if (realisasi_fisik == 0) {
                swalAlert('Realisasi fisik tidak boleh NOL!');
                return
            }

            if ((realisasi_fisik_lalu + realisasi_fisik) != total_realisasi_fisik) {
                swalAlert(
                    'Total Realisasi fisik tidak sesuai dengan realisasi fisik lalu + realisasi fisik!'
                )
                return
            }

            if ((realisasi_fisik_lalu + realisasi_fisik) > 100 || total_realisasi_fisik > 100) {
                swalAlert('Total Realisasi Fisik tidak boleh lebih dari 100%');
                return
            }

            if (total_rincian_kontrak == 0) {
                swalAlert('Total Rincian BAST tidak boleh NOL')
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
                    input_volume1: rupiah(value.volume1),
                    input_volume2: rupiah(value.volume2),
                    input_volume3: rupiah(value.volume3),
                    input_volume4: rupiah(value.volume4),
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
                swalAlert('Total detail BAST tidak sesuai dengan total di dalam rincian BAST!')
                return
            }

            let kontrak = JSON.stringify(kontrak1);

            let data = {
                jenis_kontrak,
                no_kontrak,
                id_kontrak,
                // no_pesanan,
                // tgl_pesanan,
                no_bap,
                tgl_bap,
                no_bast,
                tgl_bast,
                status_kontrak,
                keterangan,
                kd_skpd,
                nm_skpd,
                total_rincian_kontrak,
                status_anggaran,
                kontrak,
                anggaran_kontrak,
                realisasi_fisik
            };

            Swal.fire({
                title: "Apakah anda yakin menyimpan dengan status anggaran " + status_anggaran +
                    "?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, simpan!",
                cancelButtonText: "Batal!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('bast.store') }}",
                        type: "POST",
                        dataType: 'json',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            data: data
                        },
                        beforeSend: function() {
                            $('#simpan').prop('disabled', true);
                            $("#overlay").fadeIn(100);
                        },
                        success: function(data) {
                            Swal.fire({
                                title: "Berhasil!",
                                text: data.message,
                                icon: "success"
                            });

                            window.location.href = "{{ route('bast.index') }}"
                        },
                        error: function(data) {
                            $('#simpan').prop('disabled', false);
                            $("#overlay").fadeOut(100);
                            let errors = data.responseJSON;

                            Swal.fire({
                                title: "Error!",
                                html: errors.error,
                                icon: "error"
                            });
                        },
                        complete: function(data) {
                            $('#simpan').prop('disabled', false);
                            $("#overlay").fadeOut(100);
                        }
                    })
                }
            });
        });

        $('#realisasi_fisik').on('keyup', function() {
            let realisasi_fisik_lalu = rupiah($('#realisasi_fisik_lalu').val());
            let realisasi_fisik = angka($('#realisasi_fisik').val());

            $('#total_realisasi_fisik').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(realisasi_fisik_lalu + realisasi_fisik));
        });

        function load_kegiatan() {
            bersihkan('kegiatan');

            $.ajax({
                url: "{{ route('kegiatan_bast') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    "_token": "{{ csrf_token() }}",
                    status_anggaran: status_anggaran,
                    kontrak: $('#kontrak').val()
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
                url: "{{ route('rekening_bast') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: $('#kd_sub_kegiatan').val(),
                    "_token": "{{ csrf_token() }}",
                    status_anggaran: status_anggaran,
                    kontrak: $('#kontrak').val()
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
                url: "{{ route('barang_bast') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: $('#kd_sub_kegiatan').val(),
                    kd_rek6: $('#kd_rek6').val(),
                    kontrak: $('#kontrak').val(),
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
                url: "{{ route('sumber_bast') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: $('#kd_sub_kegiatan').val(),
                    kd_rek6: $('#kd_rek6').val(),
                    kd_barang: $('#kd_barang').val(),
                    kontrak: $('#kontrak').val(),
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

        function load_realisasi(sumber) {
            bersihkan('sumber');

            $.ajax({
                url: "{{ route('realisasi_bast') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    kd_sub_kegiatan: $('#kd_sub_kegiatan').val(),
                    kd_rek6: $('#kd_rek6').val(),
                    kd_barang: $('#kd_barang').val(),
                    kontrak: $('#kontrak').val(),
                    header: $('#kd_barang').find(':selected').data('header'),
                    sub_header: $('#kd_barang').find(':selected').data('sub_header'),
                    id_kontrak: $('#kontrak').find(':selected').data('id_kontrak'),
                    sumber: sumber,
                    "_token": "{{ csrf_token() }}",
                    status_anggaran: status_anggaran
                },
                beforeSend: function() {
                    $("#overlay").fadeIn(100);
                },
                success: function(data) {
                    let volume1 = $('#sumber').find(':selected').data('volume1');
                    let volume2 = $('#sumber').find(':selected').data('volume2');
                    let volume3 = $('#sumber').find(':selected').data('volume3');
                    let volume4 = $('#sumber').find(':selected').data('volume4');

                    $('#volume1').val(conversi(volume1));
                    $('#volume2').val(conversi(volume2));
                    $('#volume3').val(conversi(volume3));
                    $('#volume4').val(conversi(volume4));

                    let satuan1 = $('#sumber').find(':selected').data('satuan1');
                    let satuan2 = $('#sumber').find(':selected').data('satuan2');
                    let satuan3 = $('#sumber').find(':selected').data('satuan3');
                    let satuan4 = $('#sumber').find(':selected').data('satuan4');

                    $('#satuan1').val(satuan1);
                    $('#satuan2').val(satuan2);
                    $('#satuan3').val(satuan3);
                    $('#satuan4').val(satuan4);

                    $('#volume').val(conversi(parseFloat(volume1) * parseFloat(volume2) *
                        parseFloat(
                            volume3) * parseFloat(volume4)));
                    $('#harga').val(conversi($('#sumber').find(':selected').data('harga')));
                    $('#total').val(conversi($('#sumber').find(':selected').data('total')));

                    $('#realisasi_volume1').val(conversi(data.volume1));
                    $('#realisasi_volume2').val(conversi(data.volume2));
                    $('#realisasi_volume3').val(conversi(data.volume3));
                    $('#realisasi_volume4').val(conversi(data.volume4));
                },
                complete: function(data) {
                    $("#overlay").fadeOut(100);
                }
            });
        }

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

            $('#realisasi_volume1').val(null)
            $('#realisasi_volume2').val(null)
            $('#realisasi_volume3').val(null)
            $('#realisasi_volume4').val(null)

            $('#input_volume1').val(null)
            $('#input_volume2').val(null)
            $('#input_volume3').val(null)
            $('#input_volume4').val(null)

            $('#harga').val(null)
            $('#total').val(null)
        }

        function simpanRincian(data, response) {
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
                'aksi': `<a href="javascript:void(0);" onclick="hapusRincian('${data.id_po}','${total}')" class="btn btn-danger btn-sm"><i class="fadeIn animated bx bx-trash"></i></a>`,
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
                'aksi': `<a href="javascript:void(0);" onclick="hapusRincian('${data.id_po}','${total}')" class="btn btn-danger btn-sm"><i class="fadeIn animated bx bx-trash"></i></a>`,
            }).draw();

            let total_detail_kontrak = rupiah($('#total_detail_kontrak').val());
            let total_rincian_kontrak = rupiah($('#total_rincian_kontrak').val());

            $('#total_detail_kontrak').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_detail_kontrak + total));

            $('#total_rincian_kontrak').val(new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(total_rincian_kontrak + total));

            if (parseFloat(total_detail_kontrak + total) == parseFloat(response.sisaKontrak)) {
                $('#status_kontrak').val('1').change();
            } else {
                $('#status_kontrak').val('2').change();
            }
        }

        function isiKeterangan() {
            let jenis = $('#jenis_kontrak').val() == '2' ? 'BAP' : 'BAST';
            let nm_kerja = kontrak.data('pekerjaan');
            let tipe = kontrak.data('tipe') == '2' ? 'SP' : 'SPK';
            let nomor_kontrak = $('#kontrak').val()

            let no_bap = $('#no_bap').val();
            let no_bast = $('#no_bast').val();
            let tgl_bap = $('#tgl_bap').val();
            let tgl_bast = $('#tgl_bast').val();

            let tanggal = $('#jenis_kontrak').val() == '2' ? tgl_bap : tgl_bast;
            let nomor = $('#jenis_kontrak').val() == '2' ? no_bap : no_bast;

            let keterangan =
                `Pembayaran Atas ${upperCase(nm_kerja)} Dengan Nomor ${tipe} ${nomor_kontrak} Tanggal ${tipe} ${tanggalIndonesia(kontrak.data('tanggalkontrak'))} Dan Nomor ${jenis} ${nomor} Tanggal ${jenis} ${tanggalIndonesia(tanggal)}`

            $('#keterangan').val(keterangan)
        }

        function tanggalIndonesia(tanggal) {
            return tanggal ? new Date(tanggal).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            }) : ''
        }

        function upperCase(str) {
            return str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                return letter.toUpperCase();
            });
        }
    });

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
</script>
