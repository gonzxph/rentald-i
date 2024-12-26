<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Status</title>
    <link rel="stylesheet" href="approved_content.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Font Awesome -->
</head>
<body>
<div class="container-fluid">
    <div class="outer-box">
<!-- Header Section -->
<div class="header-container mb-4">
    <h1 class="text-left mb-3">Rejected</h1>
</div>


<div class="d-flex justify-content-between align-items-center mb-4">
    <!-- Approved Button -->
    <div class="btn-container">
    <button id="viewApprovedButton" class="btn btn-outline-danger" onclick="loadContent('approved_content.php')">Approved</button>
    </div>

    <!-- Search Bar Section -->
    <div class="search-container">
        <form class="d-flex" role="search">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
    </div>
</div>



        <!-- Table Section -->
        <div class="containert">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">No.</th>
                                    <th scope="col">First Name</th>
                                    <th scope="col">Last Name</th>
                                    <th scope="col">Pick-up Date</th>
                                    <th scope="col">Drop-off Date</th>
                                    <th scope="col">Pick-up Location</th>
                                    <th scope="col">Drop-off Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample Data -->
                                <tr>
                                    <th scope="row">1</th>
                                    <td>FNameR</td>
                                    <td>LNameR</td>
                                    <td>2024-12-23</td>
                                    <td>2024-12-25</td>
                                    <td>Location</td>
                                    <td>Location</td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>FNameR</td>
                                    <td>LNameR</td>
                                    <td>2024-12-23</td>
                                    <td>2024-12-25</td>
                                    <td>Location</td>
                                    <td>Location</td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <td>FNameR</td>
                                    <td>LNameR</td>
                                    <td>2024-12-23</td>
                                    <td>2024-12-25</td>
                                    <td>Location</td>
                                    <td>Location</td>
                                </tr>
                                <tr>
                                    <th scope="row">4</th>
                                    <td>FNameR</td>
                                    <td>LNameR</td>
                                    <td>2024-12-23</td>
                                    <td>2024-12-25</td>
                                    <td>Location</td>
                                    <td>Location</td>
                                </tr>
                                <tr>
                                    <th scope="row">5</th>
                                    <td>FNameR</td>
                                    <td>LNameR</td>
                                    <td>2024-12-23</td>
                                    <td>2024-12-25</td>
                                    <td>Location</td>
                                    <td>Location</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
