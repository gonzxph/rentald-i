<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/head.php'; ?>
    <title>Search Available Vehicle</title>
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <div class="container mt-5 vh-100">
        <!-- Search Form -->
        <div class="row">
            <div class="col-lg-4 col-md-4 mb-3">
                <div class="card">
                    <div class="card-titlesf">
                        <h5 class="card-title-text p-3 pb-2">Search and Filter</h5>
                    </div>
                    <div class="card-body">
                        <form id="bookingForm" action="search.php" method="POST">
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-users"></i></span>
                                <input id="pax" name="pax" type="number" class="form-control" value="<?= htmlspecialchars($pax ?? '') ?>" placeholder="Number of pax" required>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-white"><i class="fas fa-calendar-alt text-secondary"></i></span>
                                <input id="dateTimeInput" name="dateTimeInput" type="text" class="form-control" value="<?= htmlspecialchars($date ?? '') ?>" placeholder="Choose date and time" required>
                            </div>
                            <button type="submit" class="btn btn-dark mt-3 w-100">Search</button>
                        </form>
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger mt-3">
                                <?= htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div class="col-lg-8 col-md-8">
                <?php if (!empty($available_cars)): ?>
                    <?php foreach ($available_cars as $car): ?>
                        <div class="d-flex justify-content-between p-3 border mb-3 rounded">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-3 col-6">
                                        <img class="img-fluid" src="#" alt="" width="200px">
                                    </div>
                                    <div class="col-lg-6 col-6">
                                        <h5 class="font-weight-medium"><?= htmlspecialchars($car['car_brand']); ?></h5>
                                        <span class="mdi mdi-car"> <?= htmlspecialchars($car['car_type']); ?></span>
                                        <span class="mdi mdi-cog"> <?= htmlspecialchars($car['car_transmission_type']); ?></span>
                                        <span class="mdi mdi-car-seat"> <?= htmlspecialchars($car['car_seats']); ?> Seats</span>
                                    </div>
                                    <div class="col-lg-3 col-12 mt-5">
                                        <div class="text-end">
                                            <p class="mb-4"><strong>₱<?= number_format($car['car_rental_rate'], 2); ?></strong></p>
                                            <button id="viewDetailBtn" class="btn mb-2 w-100">VIEW DETAILS</button>
                                            <button id="bookBtn" class="btn w-100">BOOK</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>&pax=<?= urlencode($pax) ?>&dateTimeInput=<?= urlencode($date) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="alert alert-info">No cars available for the selected criteria.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
    <script src="js/main.js"></script>
</body>
</html>