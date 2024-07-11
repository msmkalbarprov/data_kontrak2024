@extends('template.app')
@section('konten')
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="card radius-10">
                <div class="card-body">
                    <h5 class="card-title">Kontrak</h5>
                    <div class="chart-container-0">
                        <canvas id="chart_kontrak"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3 col-lg-2 card_kontrak">
            <div class="card radius-10 overflow-hidden mb-0 shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary font-20">{{ $jumlahKontrak }}</p>
                            <br>
                            <div class="text-primary ms-auto font-20"><i class="fadeIn animated bx bx-file"></i></i>
                            </div>
                            <h5 class="my-0 font-14">Jumlah Kontrak</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3 col-lg-2 card_kontrak">
            <div class="card radius-10 overflow-hidden mb-0 shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary font-20">{{ rupiah($totalKontrak->up_gu + $totalKontrak->ls) }}
                            </p>
                            <br>
                            <div class="text-primary ms-auto font-20"><i class="fadeIn animated bx bx-file"></i></i>
                            </div>
                            <h5 class="my-0 font-14">Total Kontrak</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3 col-lg-2 card_kontrak">
            <div class="card radius-10 overflow-hidden mb-0 shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary font-20">{{ rupiah($totalBapBast) }}</p>
                            <br>
                            <div class="text-primary ms-auto font-20"><i class="fadeIn animated bx bx-file"></i></i>
                            </div>
                            <h5 class="my-0 font-14">BAP/BAST</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="card radius-10">
                <div class="card-body">
                    <h5 class="card-title">BAP/BAST</h5>
                    <div class="chart-container-0">
                        <canvas id="chart_bap"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 d-flex margin_top">
            <div class="card radius-10 w-100">
                <h5 class="card-title" style="padding: 14px">
                    {{ Auth::user()->role == '9C7ABFC4-9F6B-478B-91A1-3A8C4CABA3C7' ? 'Seluruh Satuan Kerja Perangkat Daerah (SKPD)' : namaSkpd($skpd) }}
                </h5>
                <div class="card-body" style="align-content: center">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="chart-container-2">
                                <div id="chart_skpd"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 margin-title">
                            <div class="card">
                                <div class="card-body">
                                    <h3 id="total_kontrak"></h3>
                                    <h5 style="text-align: right">Total</h5>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <h3 id="total_upgu"></h3>
                                    <h5 style="text-align: right">UP/GU</h5>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <h3 id="total_ls"></h3>
                                    <h5 style="text-align: right">LS</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('js')
    <style>
        @media only screen and (max-width: 600px) {
            .margin-title {
                margin-top: 100px;
            }

            .card_kontrak {
                width: 100%;
                margin-bottom: 10px
            }
        }

        @media only screen and (min-width: 600px) {
            .margin_top {
                margin-top: -250px;
            }
        }

        #chart_skpd {
            width: 100%;
            height: 300px;
        }
    </style>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            function formatter(num) {
                return new Intl.NumberFormat("id-ID", {
                    style: "currency",
                    currency: "IDR",
                    notation: "compact",
                    minimumFractionDigits: 0,
                }).format(num);
            }

            let dataKontrak = [];
            let dataBap = [];
            let rincianKontrak;

            $.ajax({
                url: "{{ route('dashboard.data') }}",
                type: "POST",
                async: false,
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    let kontrak = response.dataKontrak[0];
                    let bap = response.dataBap[0];

                    dataKontrak = [kontrak.jan, kontrak.feb, kontrak.mar, kontrak.apr,
                        kontrak.mei, kontrak.jun, kontrak.jul, kontrak.agu,
                        kontrak.sep, kontrak.okt, kontrak.nov, kontrak.des
                    ]

                    dataBap = [bap.jan, bap.feb, bap.mar, bap.apr,
                        bap.mei, bap.jun, bap.jul, bap.agu,
                        bap.sep, bap.okt, bap.nov, bap.des
                    ]

                    rincianKontrak = response.rincianKontrak[0];
                },
                error: function(e) {
                    let errors = e.responseJSON;

                    Swal.fire({
                        title: "Error!",
                        text: errors.message,
                        icon: "error"
                    });
                },
            });
            let totalUp = "{{ $totalKontrak->up_gu }}" || 0;
            let totalLs = "{{ $totalKontrak->ls }}" || 0;
            let totalKontrak = parseFloat(totalUp) + parseFloat(totalLs);

            document.getElementById('total_ls').innerText = formatter(parseFloat(totalLs));
            document.getElementById('total_upgu').innerText = formatter(parseFloat(totalUp));
            document.getElementById('total_kontrak').innerText = formatter(parseFloat(totalKontrak));

            // CHART KONTRAK

            var chart_kontrak = document
                .getElementById("chart_kontrak")
                .getContext("2d");

            var gradientStroke = chart_kontrak.createLinearGradient(0, 0, 0, 300);
            gradientStroke.addColorStop(0, "#84D9FD");
            gradientStroke.addColorStop(1, "#775FFC");

            var myChart = new Chart(chart_kontrak, {
                type: "bar",
                data: {
                    labels: [
                        "Jan",
                        "Feb",
                        "Mar",
                        "Apr",
                        "May",
                        "Jun",
                        "Jul",
                        "Agu",
                        "Sep",
                        "Okt",
                        "Nov",
                        "Des",
                    ],
                    datasets: [{
                        label: " Total Kontrak ",
                        data: dataKontrak,
                        backgroundColor: gradientStroke,
                        hoverBackgroundColor: gradientStroke,
                        borderColor: "#fff",
                        pointRadius: 6,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: "#fff",
                        borderWidth: 2,
                        borderRadius: 20,
                    }, ],
                },
                options: {
                    maintainAspectRatio: false,
                    barPercentage: 0.5,
                    categoryPercentage: 0.7,
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                        },
                    },
                },
            });

            var chart_bap = document.getElementById("chart_bap").getContext("2d");

            var gradientStroke = chart_bap.createLinearGradient(0, 0, 0, 300);
            gradientStroke.addColorStop(0, "#84D9FD");
            gradientStroke.addColorStop(1, "#775FFC");

            var myChart = new Chart(chart_bap, {
                type: "bar",
                data: {
                    labels: [
                        "Jan",
                        "Feb",
                        "Mar",
                        "Apr",
                        "May",
                        "Jun",
                        "Jul",
                        "Agu",
                        "Sep",
                        "Okt",
                        "Nov",
                        "Des",
                    ],
                    datasets: [{
                        label: " Total BAP/BAST ",
                        data: dataBap,
                        backgroundColor: gradientStroke,
                        hoverBackgroundColor: gradientStroke,
                        borderColor: "#fff",
                        pointRadius: 6,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: "#fff",
                        borderWidth: 2,
                        borderRadius: 20,
                    }, ],
                },
                options: {
                    maintainAspectRatio: false,
                    barPercentage: 0.5,
                    categoryPercentage: 0.7,
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                        },
                    },
                },
            });

            var root = am5.Root.new("chart_skpd");

            // Set themes
            // https://www.amcharts.com/docs/v5/concepts/themes/
            root.setThemes([am5themes_Animated.new(root)]);

            // Create chart
            // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/
            var chart = root.container.children.push(
                am5percent.PieChart.new(root, {
                    layout: root.verticalLayout,
                    innerRadius: am5.percent(50),
                })
            );

            // Create series
            // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Series
            var series = chart.series.push(
                am5percent.PieSeries.new(root, {
                    valueField: "value",
                    categoryField: "category",
                    alignLabels: false,
                })
            );

            series.labels.template.setAll({
                textType: "circular",
                centerX: 0,
                centerY: 0,
            });

            // Set data
            // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Setting_data
            series.data.setAll([{
                    value: rincianKontrak.up_gu,
                    category: "UP/GU"
                },
                {
                    value: rincianKontrak.ls,
                    category: "LS"
                },
            ]);

            // Create legend
            // https://www.amcharts.com/docs/v5/charts/percent-charts/legend-percent-series/
            var legend = chart.children.push(
                am5.Legend.new(root, {
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    marginTop: 15,
                    marginBottom: 15,
                })
            );

            legend.data.setAll(series.dataItems);

            // Play initial series animation
            // https://www.amcharts.com/docs/v5/concepts/animations/#Animation_of_series
            series.appear(1000, 100);
        })
    </script>
@endpush
