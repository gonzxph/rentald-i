    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="add_vehicle_content.css">
    </head>
    <body>
    <div class="outer-box">
        <div class="header-container">
            <h1>Add Vehicle</h1>
            <button id="addCarButton" class="add-car-button">+ Add car</button>
        </div>
        
        <div class="containert">
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">No.</th>
                            <th scope="col">Model</th>
                            <th scope="col">Brand</th>
                            <th scope="col">Price</th>
                            <th scope="col">Available</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $servername = "localhost";
                        $username = "root";
                        $password = "";  // If you have a password, include it here
                        $database = "dashboard_content_db";

                        // Create connection
                        $connection = new mysqli($servername, $username, $password, $database, 3308);


                        // Check connection 
                        if ($connection->connect_error) {
                            die("Connection failed: " . $connection->connect_error);
                        }

                        $sql = "SELECT * FROM dashboard_content";
                        $result = $connection->query($sql);

                        if (!$result) {
                            die("Invalid query: " . $connection->error);
                        }       

                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                            <th>" . $row['id'] . "</th>
                            <td>" . $row['model'] . "</td>
                            <td>" . $row['brand'] . "</td>
                            <td>" . $row['price'] . "</td>
                            <td>" . $row['available'] . "</td>
                            <td>
                                <button type='button' class='btn btn-primary'><i class='far fa-eye'></i></button>
                                <button type='button' class='btn btn-success'><i class='fas fa-edit'></i></button>
                                <button type='button' class='btn btn-danger'><i class='far fa-trash-alt'></i></button>
                            </td>
                        </tr>";
                        }
                        ?>

                    </tbody>
                </table>
                
                <div class="pagination">
                    <ul>
                        <li><a href="#">&lt;</a></li>
                        <li class="active"><a href="#">1</a></li>
                        <li><a href="#">2</a></li>
                        <li><a href="#">3</a></li>
                        <li><a href="#">4</a></li>
                        <li><a href="#">&gt;</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    </div>

    </body>
    </html>




