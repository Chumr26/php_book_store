<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div class="dropdown no-arrow">
            <button class="btn btn-sm btn-primary shadow-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-calendar fa-sm text-white-50"></i> <?php echo ucfirst($period ?? 'Month'); ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuButton">
                <div class="dropdown-header">Thời gian:</div>
                <a class="dropdown-item" href="?page=dashboard&period=day">Hôm nay</a>
                <a class="dropdown-item" href="?page=dashboard&period=week">Tuần này</a>
                <a class="dropdown-item" href="?page=dashboard&period=month">Tháng này</a>
                <a class="dropdown-item" href="?page=dashboard&period=year">Năm nay</a>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Doanh thu</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($statistics['total_revenue'] ?? 0); ?>đ</div>
                            <div class="text-xs text-gray-800 mt-1">Giá trị đơn TB: <?php echo number_format($statistics['avg_order_value'] ?? 0); ?>đ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Đơn hàng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($statistics['total_orders'] ?? 0); ?></div>
                            <div class="text-xs text-gray-800 mt-1">Tỷ lệ chuyển đổi: <?php echo number_format($statistics['conversion_rate'] ?? 0, 2); ?>%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Successful Orders Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Thành công</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($statistics['success_orders'] ?? 0); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Chờ xử lý</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($statistics['pending_orders'] ?? 0); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Area Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Doanh thu & đơn hàng</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Trạng thái đơn hàng</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <?php 
                        $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];
                        $i = 0;
                        foreach ($order_status_summary as $status => $data): 
                            $color = $colors[$i % count($colors)];
                            $i++;
                        ?>
                        <span class="mr-2">
                            <i class="fas fa-circle" style="color: <?php echo $color; ?>"></i> <?php echo ucfirst($status); ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Khách hàng mới</h6>
                </div>
                <div class="card-body">
                    <div class="chart-line-sm">
                        <canvas id="newCustomersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Top sách bán chạy (SL)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="topSellingBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Content Column -->
        <div class="col-lg-6 mb-4">
            <!-- Project Card Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sách bán chạy</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Tên sách</th>
                                    <th>Đã bán</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($top_selling_books)): ?>
                                    <?php foreach ($top_selling_books as $book): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($book['ten_sach']); ?></td>
                                        <td><?php echo number_format($book['total_sold']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="2" class="text-center">Chưa có dữ liệu</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <!-- Illustrations -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Đơn hàng mới</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Mã ĐH</th>
                                    <th>Khách hàng</th>
                                    <th>Tổng tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recent_orders)): ?>
                                    <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id_hoadon']; ?></td>
                                        <td><?php echo htmlspecialchars($order['ho_ten']); ?></td>
                                        <td><?php echo number_format($order['tong_tien']); ?>đ</td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center">Chưa có đơn hàng</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Page level plugins -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>

<script>
// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

function formatCurrencyVND(value) {
    try {
        return Number(value || 0).toLocaleString('vi-VN') + 'đ';
    } catch (e) {
        return value + 'đ';
    }
}

function formatNumberVI(value) {
    try {
        return Number(value || 0).toLocaleString('vi-VN');
    } catch (e) {
        return value;
    }
}

// Area Chart Example
var ctx = document.getElementById("myAreaChart");
var myLineChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: <?php 
        $labels = array_map(function($item) { return $item['date'] ?? $item['hour'] ?? $item['month']; }, $revenue_chart);
        echo json_encode($labels); 
    ?>,
    datasets: [{
            label: "Doanh thu",
      lineTension: 0.3,
      backgroundColor: "rgba(78, 115, 223, 0.05)",
      borderColor: "rgba(78, 115, 223, 1)",
      pointRadius: 3,
      pointBackgroundColor: "rgba(78, 115, 223, 1)",
      pointBorderColor: "rgba(78, 115, 223, 1)",
      pointHoverRadius: 3,
      pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
      pointHoverBorderColor: "rgba(78, 115, 223, 1)",
      pointHitRadius: 10,
      pointBorderWidth: 2,
            yAxisID: 'yRevenue',
      data: <?php 
        $values = array_map(function($item) { return $item['revenue']; }, $revenue_chart);
        echo json_encode($values); 
      ?>,
        },{
            label: "Đơn hàng",
            lineTension: 0.3,
            backgroundColor: "rgba(28, 200, 138, 0.05)",
            borderColor: "rgba(28, 200, 138, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(28, 200, 138, 1)",
            pointBorderColor: "rgba(28, 200, 138, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(28, 200, 138, 1)",
            pointHoverBorderColor: "rgba(28, 200, 138, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            yAxisID: 'yOrders',
            data: <?php 
                $orderCounts = array_map(function($item) { return $item['order_count'] ?? 0; }, $revenue_chart);
                echo json_encode($orderCounts);
            ?>
        }],
  },
  options: {
    maintainAspectRatio: false,
    layout: {
      padding: {
        left: 10,
        right: 25,
        top: 25,
        bottom: 0
      }
    },
    scales: {
      xAxes: [{
        time: {
          unit: 'date'
        },
        gridLines: {
          display: false,
          drawBorder: false
        },
        ticks: {
          maxTicksLimit: 7
        }
      }],
      yAxes: [{
                id: 'yRevenue',
                position: 'left',
        ticks: {
          maxTicksLimit: 5,
          padding: 10,
          callback: function(value, index, values) {
                        return formatCurrencyVND(value);
          }
        },
        gridLines: {
          color: "rgb(234, 236, 244)",
          zeroLineColor: "rgb(234, 236, 244)",
          drawBorder: false,
          borderDash: [2],
          zeroLineBorderDash: [2]
        }
            },{
                id: 'yOrders',
                position: 'right',
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value) {
                        return formatNumberVI(value);
                    }
                },
                gridLines: {
                    drawOnChartArea: false,
                    drawBorder: false
                }
      }],
    },
    legend: {
            display: true
    },
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      titleMarginBottom: 10,
      titleFontColor: '#6e707e',
      titleFontSize: 14,
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      intersect: false,
      mode: 'index',
      caretPadding: 10,
      callbacks: {
        label: function(tooltipItem, chart) {
          var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                    if (datasetLabel === 'Doanh thu') {
                        return datasetLabel + ': ' + formatCurrencyVND(tooltipItem.yLabel);
                    }
                    return datasetLabel + ': ' + formatNumberVI(tooltipItem.yLabel);
        }
      }
    }
  }
});

// New Customers Chart
var newCustomersCanvas = document.getElementById("newCustomersChart");
if (newCustomersCanvas) {
    var newCustomersLabels = <?php 
            $customerLabels = array_map(function($item) { return $item['date'] ?? $item['hour'] ?? $item['month']; }, $new_customers_chart ?? []);
            echo json_encode($customerLabels);
    ?>;
    var newCustomersValues = <?php 
            $customerValues = array_map(function($item) { return $item['customer_count'] ?? 0; }, $new_customers_chart ?? []);
            echo json_encode($customerValues);
    ?>;

    new Chart(newCustomersCanvas, {
        type: 'line',
        data: {
            labels: newCustomersLabels,
            datasets: [{
                label: 'Khách hàng mới',
                lineTension: 0.3,
                backgroundColor: "rgba(54, 185, 204, 0.05)",
                borderColor: "rgba(54, 185, 204, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(54, 185, 204, 1)",
                pointBorderColor: "rgba(54, 185, 204, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(54, 185, 204, 1)",
                pointHoverBorderColor: "rgba(54, 185, 204, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: newCustomersValues
            }]
        },
        options: {
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
                xAxes: [{
                    gridLines: { display: false, drawBorder: false },
                    ticks: { maxTicksLimit: 7 }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value) { return formatNumberVI(value); }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }]
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem) {
                        return 'Khách hàng mới: ' + formatNumberVI(tooltipItem.yLabel);
                    }
                }
            }
        }
    });
}

// Top selling books (bar)
var topSellingCanvas = document.getElementById("topSellingBarChart");
if (topSellingCanvas) {
    var topSellingLabels = <?php 
            $topBooks = $top_selling_books ?? [];
            $topBooks = array_slice($topBooks, 0, 8);
            $topLabels = array_map(function($book) { return $book['ten_sach'] ?? ''; }, $topBooks);
            echo json_encode($topLabels);
    ?>;
    var topSellingValues = <?php 
            $topValues = array_map(function($book) { return $book['total_sold'] ?? 0; }, $topBooks);
            echo json_encode($topValues);
    ?>;

    new Chart(topSellingCanvas, {
        type: 'bar',
        data: {
            labels: topSellingLabels,
            datasets: [{
                label: 'Số lượng bán',
                backgroundColor: "rgba(78, 115, 223, 0.7)",
                hoverBackgroundColor: "rgba(78, 115, 223, 1)",
                borderColor: "rgba(78, 115, 223, 1)",
                data: topSellingValues
            }]
        },
        options: {
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
                xAxes: [{
                    gridLines: { display: false, drawBorder: false },
                    ticks: {
                        maxTicksLimit: 8,
                        callback: function(value) {
                            if (typeof value === 'string' && value.length > 18) return value.substring(0, 18) + '…';
                            return value;
                        }
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value) { return formatNumberVI(value); }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }]
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem) {
                        return 'Đã bán: ' + formatNumberVI(tooltipItem.yLabel);
                    }
                }
            }
        }
    });
}

// Pie Chart Example
var ctx = document.getElementById("myPieChart");
var myPieChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: <?php echo json_encode(array_keys($order_status_summary)); ?>,
    datasets: [{
      data: <?php 
        $counts = array_map(function($item) { return $item['count']; }, $order_status_summary);
        echo json_encode(array_values($counts)); 
      ?>,
      backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
      hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
      hoverBorderColor: "rgba(234, 236, 244, 1)",
    }],
  },
  options: {
    maintainAspectRatio: false,
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
    },
    legend: {
      display: false
    },
    cutoutPercentage: 80,
  },
});
</script>
