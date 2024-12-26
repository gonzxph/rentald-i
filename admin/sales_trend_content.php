<!DOCTYPE html>
<lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Trend Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="sales_trend_content.css">
    
    
</head>

<div class="container-fluid">
    <div class="outer-box">
    <h1 class="text-left mb-4">Sales Trend</h1>
        <div class="card" id="revenueCard">
            <h2>Total Revenue</h2>
            <select id="revenueYear">
                <option value="2024">2024</option>
                <option value="2023">2023</option>
            </select>
            <div class="chart-placeholder"></div>
        </div>
        <div class="card" id="bookingsCard">
            <h2>Number of Bookings</h2>
            <select id="bookingsYear">
                <option value="2024">2024</option>
                <option value="2023">2023</option>
            </select>
            <div class="chart-placeholder"></div>
        </div>
        <div class="card" id="carTypeCard">
            <h2>Popular Car Type</h2>
            <select id="carTypeYear">
                <option value="2024">2024</option>
                <option value="2023">2023</option>
            </select>
            <div class="chart-placeholder"></div>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
