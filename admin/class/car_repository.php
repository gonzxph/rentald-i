<?php

class CarRepository extends Dbh {
    public function getAllCars() {
        $sql = "SELECT * FROM car"; // Replace 'car' with your table name if different
        $stmt = $this->connect()->query($sql);
        
        $cars = [];
        while ($row = $stmt->fetch_assoc()) {
            $cars[] = new Car(
                $row["car_id"],
                $row["car_model"],
                $row["car_brand"],
                $row["car_availability"]
            );
        }

        return $cars;
    }

    public function getCarById($car_id) {
        $sql = "SELECT * FROM car WHERE car_id = ?";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$car_id]);
        $row = $stmt->fetch();

        if ($row) {
            return new Car(
                $row["car_id"],
                $row["car_model"],
                $row["car_brand"],
                $row["car_availability"]
            );
        }

        return null; // If no car is found
    }

    public function addCar(Car $car) {
        $sql = "INSERT INTO car (car_description, car_brand, car_model, car_year, car_type, car_color, car_seats, 
                car_transmission_type, car_fuel_type, car_rental_rate, car_excess_per_hour, car_availability) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([
            $car->getDescription(),
            $car->getBrand(),
            $car->getModel(),
            $car->getYear(),
            $car->getType(),
            $car->getColor(),
            $car->getSeats(),
            $car->getTransmissionType(),
            $car->getFuelType(),
            $car->getRentalRate(),
            $car->getExcessPerHour(),
            $car->getAvailability()
        ]);
    }

    public function updateCar(Car $car) {
        $sql = "UPDATE car 
                SET car_description = ?, car_brand = ?, car_model = ?, car_year = ?, car_type = ?, car_color = ?, 
                    car_seats = ?, car_transmission_type = ?, car_fuel_type = ?, car_rental_rate = ?, 
                    car_excess_per_hour = ?, car_availability = ? 
                WHERE car_id = ?";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([
            $car->getDescription(),
            $car->getBrand(),
            $car->getModel(),
            $car->getYear(),
            $car->getType(),
            $car->getColor(),
            $car->getSeats(),
            $car->getTransmissionType(),
            $car->getFuelType(),
            $car->getRentalRate(),
            $car->getExcessPerHour(),
            $car->getAvailability(),
            $car->getId()
        ]);
    }

    public function deleteCar($car_id) {
        $sql = "DELETE FROM car WHERE car_id = ?";
        $stmt = $this->connect()->prepare($sql);
        $stmt->bind_param("i", $car_id);
        $stmt->execute();
    }
    

}
