

addEventListener("DOMContentLoaded", (event) => {
    get_dashboadCards() ;
});

function get_dashboadCards() {
	$.post(`${base_url}/app/dashboard_data.php?action=get&endpoint=cards`, {}, function (data) {
		console.log(data)
		let res = JSON.parse(data);

		$('.company_balance').html(formatMoney(res.company_balance))
		$('.total_expenses').html(formatMoney(res.total_expenses))
		$('.upcoming_salary').html(formatMoney(res.upcoming_salary))
		$('.coming_date').html(res.coming_date)
	});

	let chartData = [0,0,0,0,0,0,0,0,0,0,0,0];
	$.post(`${base_url}/app/dashboard_data.php?action=get&endpoint=chart_bar`, {}, function (data) {
		console.log(data)
		let res = JSON.parse(data);
		console.log(res)
		chartData = res

		var options = {
        series: [{
            name: "Sales",
            data: chartData
        }],
        chart: {
            //width:150,
            foreColor: "#9ba7b2",
            height: 235,
            type: 'bar',
            toolbar: {
                show: !1,
            },
            sparkline: {
                enabled: !1
            },
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: 4,
            curve: 'smooth',
            colors: ['transparent']
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                gradientToColors: ['#50b848', '#0074be'],
                shadeIntensity: 1,
                type: 'vertical',
                //opacityFrom: 0.8,
                //opacityTo: 0.1,
                stops: [0, 100, 100, 100]
            },
        },
        colors: ['#50b848', "#0074be"],
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 4,
                borderRadiusApplication: 'around',
                borderRadiusWhenStacked: 'last',
                columnWidth: '55%',
            }
        },
        grid: {
            show: false,
            borderColor: 'rgba(0, 0, 0, 0.15)',
            strokeDashArray: 4,
        },
        tooltip: {
            theme: "dark",
            fixed: {
                enabled: !0
            },
            x: {
                show: !0
            },
            y: {
                title: {
                    formatter: function (e) {
                        return ""
                    }
                }
            },
            marker: {
                show: !1
            }
        },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart5"), options);
    chart.render();

	});
    
}