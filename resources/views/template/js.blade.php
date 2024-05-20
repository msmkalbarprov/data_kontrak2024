<!-- Bootstrap JS -->
<script src="{{ asset('template/assets/js/bootstrap.bundle.min.js') }}"></script>
<!--plugins-->
<script src="{{ asset('template/assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('template/assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ asset('template/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('template/assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
<script src="{{ asset('template/assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ asset('template/assets/plugins/chartjs/js/chart.js') }}"></script>
<script src="{{ asset('template/assets/js/index.js') }}"></script>
<!--app JS-->
<script src="{{ asset('template/assets/js/app.js') }}"></script>

<script src="{{ asset('template/assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('template/assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"
    integrity="sha512-4MvcHwcbqXKUHB6Lx3Zb5CEAVoE9u84qN+ZSMM6s7z8IeJriExrV3ND5zRze9mxNlABJ6k864P/Vl8m0Sd3DtQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.8/sweetalert2.min.js"
    integrity="sha512-FbWDiO6LEOsPMMxeEvwrJPNzc0cinzzC0cB/+I2NFlfBPFlZJ3JHSYJBtdK7PhMn0VQlCY1qxflEG+rplMwGUg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    $('.select_option').select2({
        theme: "bootstrap-5",
        width: "100%"
    });

    $('.select_modal').select2({
        dropdownParent: $('#modal_cetak .modal-content'),
        theme: 'bootstrap-5'
    });

    $("input[data-type='currency']").on({
        keyup: function() {
            formatCurrency($(this));
        },
        blur: function() {
            formatCurrency($(this), "blur");
        }
    });

    function formatNumber(n) {
        // format number 1000000 to 1,234,567
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    }

    function formatCurrency(input, blur) {
        // appends $ to value, validates decimal side
        // and puts cursor back in right position.

        // get input value
        var input_val = input.val();

        // don't validate empty input
        if (input_val === "") {
            return;
        }

        // original length
        var original_len = input_val.length;

        // initial caret position
        var caret_pos = input.prop("selectionStart");

        // check for decimal
        if (input_val.indexOf(".") >= 0) {

            // get position of first decimal
            // this prevents multiple decimals from
            // being entered
            var decimal_pos = input_val.indexOf(".");

            // split number by decimal point
            var left_side = input_val.substring(0, decimal_pos);
            var right_side = input_val.substring(decimal_pos);

            // add commas to left side of number
            left_side = formatNumber(left_side);

            // validate right side
            right_side = formatNumber(right_side);

            // On blur make sure 2 numbers after decimal
            if (blur === "blur") {
                right_side += "00";
            }

            // Limit decimal to only 2 digits
            right_side = right_side.substring(0, 2);

            // join number by .
            input_val = left_side + "." + right_side;

        } else {
            // no decimal entered
            // add commas to number
            // remove all non-digits
            input_val = formatNumber(input_val);
            input_val = input_val;

            // final formatting
            if (blur === "blur") {
                input_val += ".00";
            }
        }

        // send updated string to input
        input.val(input_val);

        // put caret back in the right position
        var updated_len = input_val.length;
        caret_pos = updated_len - original_len + caret_pos;
        input[0].setSelectionRange(caret_pos, caret_pos);
    }

    function rupiah(n) {
        let n1 = n.split('.').join('');
        let rupiah = n1.split(',').join('.');
        return parseFloat(rupiah) || 0;
    }

    function angka(n) {
        let nilai = n.split(',').join('');
        return parseFloat(nilai) || 0;
    }

    function conversi(data) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2
        }).format(data);
    }

    function swalAlert(message) {
        Swal.fire({
            icon: "warning",
            title: "Oops...",
            text: message,
        });
    }

    $('.ringkasan').on('click', function() {
        let no_kontrak = $('#no_kontrak').val();
        let id_kontrak = $('#id_kontrak').val();
        let kd_skpd = $('#kd_skpd').val();
        let pptk = $('#pptk').val();
        let tanggal_ttd = $('#tanggal_ttd').val();
        let margin_atas = $('#margin_atas').val();
        let margin_bawah = $('#margin_bawah').val();
        let margin_kanan = $('#margin_kanan').val();
        let margin_kiri = $('#margin_kiri').val();
        let jenis_print = $(this).data("jenis");

        if (!pptk) {
            swalAlert('Silahkan pilih PPTK');
            return
        }

        if (!tanggal_ttd) {
            swalAlert('Silahkan isi tanggal ttd');
            return
        }

        let url = new URL("{{ route('laporan_kontrak.cetak') }}");
        let searchParams = url.searchParams;
        searchParams.append("no_kontrak", no_kontrak);
        searchParams.append("id_kontrak", id_kontrak);
        searchParams.append("kd_skpd", kd_skpd);
        searchParams.append("pptk", pptk);
        searchParams.append("tanggal_ttd", tanggal_ttd);
        searchParams.append("margin_atas", margin_atas);
        searchParams.append("margin_bawah", margin_bawah);
        searchParams.append("margin_kanan", margin_kanan);
        searchParams.append("margin_kiri", margin_kiri);
        searchParams.append("jenis_print", jenis_print);
        window.open(url.toString(), "_blank");
    });

    function cetak(id, nomorkontrak, kd_skpd) {
        $('#no_kontrak').val(nomorkontrak);
        $('#id_kontrak').val(id);
        $('#kd_skpd').val(kd_skpd);
        $('#modal_cetak').modal('show');
    }

    function trim(n) {
        return n.trim()
    }
</script>
@stack('js')
