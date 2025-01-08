<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Trend Dashboard</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="sales_trend_content.css">
    <?php include 'admin_header/admin_header.php'; include 'admin_header/admin_nav.php'; ?>
</head>

<body>
<div class="container-fluid" style="margin-top:30px;">
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
`
        <div class="back-btn-container">
                <button type="button" class="btn btn-primary" onclick="goToDashboard()" style="font-size: 1rem; margin-top:10px;">Back</button>
        </div>
    </div>
  
</div>


<script>
    // Test if JavaScript is working
    console.log("JavaScript is working!");

    function goToDashboard() {
            window.location.href = 'index.php';
        }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
