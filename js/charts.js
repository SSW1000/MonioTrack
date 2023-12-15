$(document).ready(function () {
var ctxIncome = document.getElementById('incomeByCategoryChart').getContext('2d');
    var myChartIncome = new Chart(ctxIncome, {
        type: 'pie',
        data: {
            labels: categoryLabels,
            datasets: [{
                label: 'Income by Categories',
                data: categoryAmounts,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(0, 128, 0, 0.7)',
                    'rgba(255, 0, 255, 0.7)',
                    'rgba(255, 0, 0, 0.7)',
                    'rgba(0, 0, 255, 0.7)',
                    'rgba(128, 0, 128, 0.7)',
                    'rgba(0, 255, 0, 0.7)',
                    'rgba(128, 128, 0, 0.7)',
                    'rgba(0, 255, 255, 0.7)',
                    'rgba(192, 192, 192, 0.7)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(0, 128, 0, 1)',
                    'rgba(255, 0, 255, 1)',
                    'rgba(255, 0, 0, 1)',
                    'rgba(0, 0, 255, 1)',
                    'rgba(128, 0, 128, 1)',
                    'rgba(0, 255, 0, 1)',
                    'rgba(128, 128, 0, 1)',
                    'rgba(0, 255, 255, 1)',
                    'rgba(192, 192, 192, 1)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    enabled: true,
                },
                datalabels: {
                    color: '#fff',
                    formatter: (value, ctx) => {
                        return ctx.chart.data.labels[ctx.dataIndex] + '\n' + value;
                    },
                    anchor: 'end',
                    align: 'start',
                    offset: 4,
                }
            }
        }
    });

    // Chart for expenses breakdown
    var ctxExpenses = document.getElementById('expensesByCategoryChart').getContext('2d');
    var myChartExpenses = new Chart(ctxExpenses, {
        type: 'pie',
        data: {
            labels: categoryExpensesLabels,
            datasets: [{
                label: 'Expenses by Categories',
                data: categoryExpensesAmounts,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(0, 128, 0, 0.7)',
                    'rgba(255, 0, 255, 0.7)',
                    'rgba(255, 0, 0, 0.7)',
                    'rgba(0, 0, 255, 0.7)',
                    'rgba(128, 0, 128, 0.7)',
                    'rgba(0, 255, 0, 0.7)',
                    'rgba(128, 128, 0, 0.7)',
                    'rgba(0, 255, 255, 0.7)',
                    'rgba(192, 192, 192, 0.7)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(0, 128, 0, 1)',
                    'rgba(255, 0, 255, 1)',
                    'rgba(255, 0, 0, 1)',
                    'rgba(0, 0, 255, 1)',
                    'rgba(128, 0, 128, 1)',
                    'rgba(0, 255, 0, 1)',
                    'rgba(128, 128, 0, 1)',
                    'rgba(0, 255, 255, 1)',
                    'rgba(192, 192, 192, 1)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    enabled: true,
                },
                datalabels: {
                    color: '#fff',
                    formatter: (value, ctx) => {
                        return ctx.chart.data.labels[ctx.dataIndex] + '\n' + value;
                    },
                    anchor: 'end',
                    align: 'start',
                    offset: 4,
                }
            }
        }
    });
    var ctxIncomeVsExpensesDonut = document.getElementById('incomeVsExpensesDonutChart').getContext('2d');
    var myChartIncomeVsExpensesDonut = new Chart(ctxIncomeVsExpensesDonut, {
        type: 'doughnut',
        data: {
            labels: ['Income', 'Expenses'],
            datasets: [{
                label: 'Income vs Expenses',
                data: [totalIncomeMonth,totalExpensesMonth],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.5)', // Income color with transparency
                    'rgba(255, 99, 132, 0.5)', // Expenses color with transparency
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)', // Income border color
                    'rgba(255, 99, 132, 1)', // Expenses border color
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '80%', // Adjust the cutout percentage to control the size of the donut hole
        }
    });

    myChartIncome.options.plugins.legend.display = false;
    myChartIncome.update();

    myChartExpenses.options.plugins.legend.display = false;
    myChartExpenses.update();

    myChartIncomeVsExpensesDonut.options.plugins.legend.display = false;
    myChartIncomeVsExpensesDonut.update();
});