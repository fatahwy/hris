$(document).on('click', '[data-confirm]', function (e) {
    e.preventDefault();

    var $this = $(this);
    var message = $this.data('confirm');
    var method = $this.data('method') || 'get';
    var url = $this.attr('href');

    Swal.fire({
        title: 'Konfirmasi',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            if (method.toLowerCase() === 'post') {
                var form = $('<form>', {
                    method: 'post',
                    action: url
                });

                var csrfParam = yii.getCsrfParam();
                var csrfToken = yii.getCsrfToken();

                form.append($('<input>', {
                    type: 'hidden',
                    name: csrfParam,
                    value: csrfToken
                }));

                $('body').append(form);
                form.submit();
            } else {
                window.location.href = url;
            }
        }
    });
});

init();
function init() {
    btnSubmitLoding();

    $("document").on("pjax:end", '.pjax-filter', function () {
        btnSubmitLoding();
    });

    $('input[type="number"]').on('keyup change paste input', function (e) {
        this.value = this.value < 0 ? 0 : this.value;
    });

    $('.modalButton').click(function () {
        $('#header-modal').html($(this).attr('modal-header'));
        $('#modal').modal('show')
            .find('#modalContent')
            .html('Loading...')
            .load($(this).attr('value'));
    });

    $('#dynamic-form').on('keydown', 'input, select', function (e) {
        return disableEnter(e);
    });

    window.setInterval(function () {
        $('select, input, textarea').removeClass('is-valid');

        // button file input
        $('.kv-file-remove').remove();

        const $lastRow = $(".grid-view thead tr").not(".skip-export, .header-row").last();
        if ($lastRow) {
            $lastRow.addClass('last')
        }
    }, 1000);

    initDate();
    initDecimal();
}

function toDecimal(selector, value) {
    $(selector).val(parseFloat(value).toFixed(2));
}

function toInteger(selector, value) {
    $(selector).val(parseInt(value));
}

function toIntDecimalNumber(value) {
    if (value.length > 0) {
        return parseFloat(value.replace('.', '').replace(',', '.'));
    }
    return value;
}

function getOptions() {
    return {
        'alias': 'decimal',
        'radixPoint': ',',
        'groupSeparator': '.',
        'digits': 2,
        'autoGroup': true,
        'autoUnmask': true,
        'unmaskAsNumber': true,
        'removeMaskOnSubmit': true
    };
}

function initDecimal() {
    $('.number').prop('type', 'number').prop('step', '.01');
}

function initDate() {
    $('.mask-date').attr('placeholder', 'dd-mm-yyyy');
    $('body').on('keyup', '.mask-date', function (e) {
        let value = this.value.replace(/\D/g, ''); // Remove non-digit characters
        if (value.length > 8) {
            value = value.slice(0, 8); // Limit to 8 digits (MMDDYYYY)
        }

        const parts = [];
        if (value.length > 2) {
            parts.push(value.slice(0, 2));
            value = value.slice(2);
        }
        if (value.length > 2) {
            parts.push(value.slice(0, 2));
            value = value.slice(2);
        }
        if (value.length > 4) {
            parts.push(value.slice(0, 4));
        } else {
            parts.push(value);
        }

        this.value = parts.join('-');
    })

    // $('.mask-date').inputmask('dd-mm-yyyy');
}

function btnLoading(target) {
    var tmp = target.html();
    target.html('<span class="bi bi-arrow-repeat spin"></span> Loading ...');
    target.prop('disabled', true);
    return tmp;
}

function btnSubmitLoding() {
    $('body').on('beforeSubmit', 'form', function () {
        btnLoading($(this).find('button[type="submit"]'));
    });
}

function numberFormat(val, config = {}) {
    const formatter = new Intl.NumberFormat('id-ID', Object.assign({
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }, config));

    return formatter.format(val);
}

function toCurrency(val, isPlain = false) {
    return numberFormat(val, {
        style: 'currency',
        currency: 'IDR',
    })
}

function formatDate(date = new Date()) {
    let d = new Date(date);
    let month = (d.getMonth() + 1).toString();
    let day = d.getDate().toString();
    let year = d.getFullYear();

    if (month.length < 2) {
        month = '0' + month;
    }

    if (day.length < 2) {
        day = '0' + day;
    }

    return [year, month, day].join('-');
}

let popupWindow = null;

function print_report(url) {
    if (popupWindow && !popupWindow.closed) {
        popupWindow.close();
    }

    popupWindow = window.open(url, 'popUpWindow', 'height=500,width=800,left=1000,top=100,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no, status=yes');
}

function disableEnter(e) {
    const keyCode = e.keyCode || e.which;

    if (keyCode === 13) {
        e.preventDefault();
        return false;
    }
}

const plugins = {
    id: 'custom_canvas_background_color',
    beforeDraw: (chart, args, options) => {
        const { ctx } = chart;

        if (chart.config.options.elements.center) {
            const txt = chart.config.options.elements.center;

            const centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
            const centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 2);
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.font = (chart.height * 1 / 10) + "px Arial";
            ctx.fillStyle = '#000';

            ctx.fillText(txt, centerX, centerY);
        } else {
            ctx.save();
            ctx.globalCompositeOperation = 'destination-over';
            ctx.fillStyle = "rgba(255, 255, 255)";
            ctx.fillRect(0, 0, chart.width, chart.height);
            ctx.restore();
        }
    }
};

const legendMargin = {
    id: 'legendMargin',
    afterInit(chart, args, plugins) {
        const originalFit = chart.legend.fit;
        const margin = plugins.margin || 0;
        chart.legend.fit = function fit() {
            if (originalFit) {
                originalFit.call(this)
            }
            return this.height += margin;
        }
    }
}

function createChart(labels, datasets, type = 'line', selector = '', options = {}, showLabel = false) {
    const ctx = document.querySelector('.' + (selector ? selector : 'myChart')).getContext('2d');
    const plgns = options.plugins ? options.plugins : plugins;
    const color = '#343a40';

    switch (type) {
        case 'scatter':
            const quadrants = {
                id: 'quadrants',
                beforeDraw(chart, args, options) {
                    const { ctx, chartArea: { left, top, right, bottom }, scales: { x, y } } = chart;
                    const midX = x.getPixelForValue(0);
                    const midY = y.getPixelForValue(0);
                    ctx.save();
                    ctx.fillStyle = options.topLeft;
                    ctx.fillRect(left, top, midX - left, midY - top);
                    ctx.fillStyle = options.topRight;
                    ctx.fillRect(midX, top, right - midX, midY - top);
                    ctx.fillStyle = options.bottomRight;
                    ctx.fillRect(midX, midY, right - midX, bottom - midY);
                    ctx.fillStyle = options.bottomLeft;
                    ctx.fillRect(left, midY, midX - left, bottom - midY);
                    ctx.restore();
                }
            };

            new Chart(ctx, {
                type: 'scatter',
                data: { labels, datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            min: -100,
                            max: 100,
                            position: 'center',
                        },
                        y: {
                            min: -100,
                            max: 100,
                            position: 'center',
                        },
                    },
                    plugins: {
                        quadrants: {
                            topLeft: 'rgb(255, 99, 132)',
                            topRight: 'rgb(54, 162, 235)',
                            bottomRight: 'rgb(75, 192, 192)',
                            bottomLeft: 'rgb(255, 205, 86)',
                        },
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    let label = labels[ctx.datasetIndex];
                                    return label;
                                }
                            }
                        },
                        // datalabels: {
                        //     backgroundColor: function (context) {
                        //         return context.dataset.backgroundColor;
                        //     },
                        //     borderRadius: 4,
                        //     color: 'white',
                        //     font: {
                        //         weight: 'bold'
                        //     },
                        //     padding: 6,
                        //     formatter: function(value, ctx) {
                        //         let label = labels[ctx.dataIndex];
                        //         return label;
                        //       },
                        // }
                    }
                },
                plugins: [
                    // ChartDataLabels,
                    // quadrants,
                ],
            });
            break;
        case 'barx':
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets
                },
                options: {
                    scales: {
                        y: {
                            ticks: {
                                padding: 20 // Increases space between labels and the Y-axis
                            }
                        },
                    },
                    indexAxis: 'y',
                    // Elements options apply to all of the options unless overridden in a dataset
                    // In this case, we are setting the border of each horizontal bar to be 2px wide
                    elements: {
                        bar: {
                            borderWidth: 2,
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: false,
                        title: {
                            display: options.title !== undefined,
                            text: options.title
                        },
                        datalabels: {
                            color,
                            formatter: function (value, context) {
                                if (value > 0) {
                                    return numberFormat(value);
                                }
                                return null;
                            }
                        }
                    },
                },
                plugins: [
                    showLabel ? ChartDataLabels : {}
                ],
            });
            break;
        case 'barstackedx':
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            stacked: true,
                            ticks: {
                                padding: 20 // Increases space between labels and the Y-axis
                            }
                        }
                    },
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: {
                            color,
                            formatter: function (value, context) {
                                if (value) {
                                    return numberFormat(value);
                                }
                                return null;
                            }
                        }
                    },
                    elements: {
                        center: options.centerText !== undefined ? options.centerText : ''
                    }
                },
                plugins: [
                    showLabel ? ChartDataLabels : {}
                ],
            });
            break;
        case 'barstacked':
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            stacked: true,
                        }
                    }
                }
            });
            break;
        default:
            new Chart(ctx, {
                type,
                data: {
                    labels,
                    datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legendMargin: {
                            margin: 20,   // Adjust this value to control the space (in pixels)
                        },
                        colors: {
                            enabled: false
                        },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                footer: (ttItem) => {
                                    if (['pie'].includes(type)) {
                                        let sum = 0;
                                        let dataArr = ttItem[0].dataset.data;
                                        dataArr.map(data => {
                                            sum += Number(data);
                                        });

                                        let percentage = (ttItem[0].parsed * 100 / sum).toFixed(2) + '%';
                                        return `Persentase : ${percentage}`;
                                    }
                                }
                            }
                        },
                        title: {
                            display: options.title !== undefined,
                            text: options.title
                        },
                        datalabels: {
                            color,
                            anchor: 'end',
                            align: 'top',
                            formatter: function (value, context) {
                                if (value) {
                                    return numberFormat(value);
                                }
                                return null;
                            }
                        }
                    },
                    elements: {
                        center: options.centerText !== undefined ? options.centerText : ''
                    }
                },
                plugins: [
                    plgns,
                    legendMargin,
                    showLabel ? ChartDataLabels : {}
                ]
            });
            break;
    }
}

function isNumeric(n) {
    'use strict';
    n = String(n);
    n = n.replace(/\./g, '').replace(',', '');
    return !isNaN(parseFloat(n)) && isFinite(n);
}

$('.datatable thead tr').removeAttr('id')

function setupDatatable(selector, filters = []) {
    $(selector).dataTable({
        scrollX: true,
        orderCellsTop: true,
        paging: false,
        info: false,
        dom: 'lrtip',
        columnDefs: [{
            targets: '_all',
            createdCell: function (td, cellData, rowData, row, col) {
                if (isNumeric(cellData)) {
                    $(td).html(numberFormat(cellData));
                }
            }
        }],
        initComplete: function () {
            this.api().columns().every(function () {
                var column = this;

                switch (filters[column.index()]) {
                    case 'select':
                        var select = $('<select class="form-control"><option value=""></option></select>')
                            .appendTo($(selector + " thead tr:eq(1) td").eq(column.index()).empty())
                            .on('change', function () {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                column.search(val ? '^' + val + '$' : '', true, false).draw();
                            });

                        column.data().unique().sort().each(function (d, j) {
                            select.append('<option value="' + d + '">' + d + '</option>');
                        });
                        break;
                    case 'input':
                        let input = $('<input class="form-control" type="text">')
                            .appendTo($(selector + " thead tr:eq(1) td").eq(column.index()).empty())
                            .on('keyup', function () {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                column.search(val).draw();
                            });
                        break;
                    default:
                        $(selector + " thead tr:eq(1) td").eq(column.index()).empty()
                        break;
                }
            });
        }
    });
}

$('body').on('keyup', '.integer', function (e) {
    if (this.value != "") {
        if (this.min && parseInt(this.value) < parseInt(this.min)) {
            this.value = this.min;
        }
        if (this.max && parseInt(this.value) > parseInt(this.max)) {
            this.value = this.max;
        }
    }
})