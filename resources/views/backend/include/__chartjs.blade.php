<script>
    (function($) {
        'use strict';

        //site chart
        let chart;

        let startDate= '{{ $data['start_date'] }}';
        let endDate = '{{ $data['end_date'] }}';

        $('input[name="site_daterange"]').daterangepicker({
            opens: 'left'
        }, function(start, end) {
            startDate = start.format('YYYY-MM-DD');
            endDate = end.format('YYYY-MM-DD');
            getChartData();
        });

        $('#currency').on('change', function() {
            getChartData();
        });

        function getChartData() {
            $.get('{{ route('admin.dashboard') }}?type=site', {
                start_date: startDate,
                end_date: endDate,
                currency: $('#currency').val()
            }, function(chartData) {
                chart.destroy();
                siteStatisticsChart(chartData);
            });
        }

        function siteStatisticsChart(chartData) {

            var date_label = Object.keys(chartData['date_label']);
            var total_deposit = Object.values(chartData['deposit_statistics']);
            var total_withdraw = Object.values(chartData['withdraw_statistics']);
            var total_cashout = Object.values(chartData['cashout_statistics']);
            var total_payment = Object.values(chartData['payment_statistics']);
            var total_transfer = Object.values(chartData['transfer_statistics']);
            var symbol = chartData['symbol'];

            // Bar Chart
            var data = {
                labels: date_label,
                datasets: [{
                        label: '{{ __('Total Deposit') }}' + ' ' + symbol + sumArrayValues(total_deposit),
                        data: total_deposit,
                        backgroundColor: '#5e3fc9',
                        borderColor: '#ffffff',
                        borderWidth: 0,
                        borderRadius: 90,
                        tension: 0.1
                    },
                    {
                        label: '{{ __('Total Withdraw') }}' + ' ' + symbol + sumArrayValues(total_withdraw),
                        data: total_withdraw,
                        backgroundColor: '#ffc300',
                        borderColor: '#ffffff',
                        borderWidth: 0,
                        borderRadius: 90,
                        tension: 0.1
                    },
                    {
                        label: '{{ __('Total Cashout') }}' + ' ' + symbol + sumArrayValues(total_cashout),
                        data: total_cashout,
                        backgroundColor: '#ef476f',
                        borderColor: '#ffffff',
                        borderWidth: 0,
                        borderRadius: 90,
                        tension: 0.1
                    },
                    {
                        label: '{{ __('Total Payment') }}' + ' ' + symbol + sumArrayValues(total_payment),
                        data: total_payment,
                        backgroundColor: '#2a9d8f',
                        borderColor: '#ffffff',
                        borderWidth: 0,
                        borderRadius: 90,
                        tension: 0.1
                    },
                    {
                        label: '{{ __('Total Transfer') }}' + ' ' + symbol + sumArrayValues(total_transfer),
                        data: total_transfer,
                        backgroundColor: '#003566',
                        borderColor: '#ffffff',
                        borderWidth: 0,
                        borderRadius: 90,
                        tension: 0.1
                    }
                ]
            };
            // render init block

            var ctx = document.getElementById('statisticsChart');
            var configuration = {
                type: 'bar',
                data,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return (context.dataset.label.split(symbol)[0]).split(' ')[1] + ': ' +
                                        symbol + context.formattedValue;
                                }
                            }
                        }
                    }
                }
            }

            if (chart) {
                chart.destroy();
                chart = new Chart(ctx, configuration);
            } else {
                chart = new Chart(ctx, configuration);
            }
        }

        var chartData = {
            'date_label': @json($data['date_label']),
            'withdraw_statistics': @json($data['withdraw_statistics']),
            'deposit_statistics': @json($data['deposit_statistics']),
            'cashout_statistics': @json($data['cashout_statistics']),
            'payment_statistics': @json($data['payment_statistics']),
            'transfer_statistics': @json($data['transfer_statistics']),
            'symbol': @json($data['symbol']),
        };

        siteStatisticsChart(chartData);

        // Country Chart
        var country = @json($data['country']);
        var country_data = Object.values(country);
        var country_label = Object.keys(country);
        var data = {
            labels: country_label,
            datasets: [{
                label: 'Country',
                data: country_data,
                backgroundColor: [
                    '#5e3fc9',
                    '#2a9d8f',
                    '#ef476f',
                    '#718355',
                    '#ee6c4d',
                    '#6d597a',
                    '#003566',
                    "#b91d47",
                    "#00aba9",
                    "#2b5797",
                    "#e8c3b9",
                    "#1e7145"
                ],
                borderColor: [
                    '#ffffff',
                    '#ffffff',
                    '#ffffff',
                    '#ffffff',
                    '#ffffff',
                    '#ffffff',
                    '#ffffff'
                ],
                borderWidth: 3,
                borderRadius: 12,
                barPercentage: 0.3,
                hoverBackgroundColor: '#003566',
            }]
        };
        // render init block
        new Chart(
            document.getElementById('countryChart'), {
                type: 'doughnut',
                data,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            }
        );

        // Browser Chart
        var browser = @json($data['browser']);
        var browser_data = Object.values(browser);
        var browser_label = Object.keys(browser);
        var data = {
            labels: browser_label,
            datasets: [{
                label: 'Browser',
                data: browser_data,
                backgroundColor: [
                    '#5e3fc9',
                    '#2a9d8f',
                    '#ef476f',
                    '#718355',
                    '#ee6c4d',
                    '#6d597a',
                    '#003566',
                    "#b91d47",
                    "#00aba9",
                    "#2b5797",
                    "#e8c3b9",
                    "#1e7145"
                ],
                borderColor: [
                    '#ffffff',
                    '#ffffff',
                    '#ffffff',
                    '#ffffff',
                    '#ffffff',
                    '#ffffff',
                    '#ffffff'
                ],
                borderWidth: 2,
                borderRadius: 12,
                barPercentage: 0.3,
                hoverBackgroundColor: '#003566',
            }]
        };
        // render init block
        new Chart(
            document.getElementById('browserChart'), {
                type: 'polarArea',
                data,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            }
        );

        // OS Chart
        var platform = @json($data['platform']);
        var platform_data = Object.values(platform);
        var platform_label = Object.keys(platform);
        var data = {
            labels: platform_label,
            datasets: [{
                label: 'OS',
                data: platform_data,
                backgroundColor: [
                    '#5e3fc9',
                    '#718355',
                    '#ef476f',
                    '#ee6c4d',
                    "#b91d47",
                    "#2b5797",
                    "#e8c3b9",
                    "#1e7145",
                    '#2a9d8f',
                ],
                borderColor: [
                    '#ffffff',
                    '#ffffff',
                    '#ffffff',
                    '#ffffff',
                    '#ffffff',
                    '#ffffff',
                    '#ffffff'
                ],
                borderWidth: 3,
                borderRadius: 12,
                barPercentage: 0.3,
                hoverBackgroundColor: '#003566',
            }]
        };
        // render init block
        new Chart(
            document.getElementById('osChart'), {
                type: 'pie',
                data,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            }
        );

    })(jQuery);
</script>
