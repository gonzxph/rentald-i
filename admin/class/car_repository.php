<?php


class CarRepository extends Dbh {
    public function getAllCars() {
        $sql = "SELECT * FROM dashboard_content"; // Replace 'cars' with your table name
        $stmt = $this->connect()->query($sql);
        
        $cars = [];
        while ($row = $stmt->fetch_assoc()) {
            $cars[] = new Car(
                $row["id"],
                $row["model"],
                $row["brand"],
                $row["price"],
                $row["available"],
                 /*
                $row['id'],
                $row['brand'],
                $row['color'],
                $row['year'],
                $row['license_plate'],
                $row['vin'],
                $row['seats'],
                $row['transmission_type'],
                $row['fuel_type'],
                $row['availability']
                */
            );
        }

        return $cars;
    }

    public function getCarById($id) {
        $sql = "SELECT * FROM dashboard_content WHERE id = ?";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if ($row) {
            return new Car(
                $row['id'],
                $row['brand'],
                $row['color'],
                $row['year'],
                $row['license_plate'],
                $row['vin'],
                $row['seats'],
                $row['transmission_type'],
                $row['fuel_type'],
                $row['availability']
            );
        }

        return null; // If no car is found
    }

    public function addCar(Car $car) {
        $sql = "INSERT INTO dashboard_content (brand, color, year, license_plate, vin, seats, transmission_type, fuel_type, availability) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([
            $car->getBrand(),
            $car->getColor(),
            $car->getYear(),
            $car->getLicensePlate(),
            $car->getVin(),
            $car->getSeats(),
            $car->getTransmissionType(),
            $car->getFuelType(),
            $car->getAvailability()
        ]);
    }

    public function updateCar(Car $car) {
        $sql = "UPDATE dashboard_content 
                SET brand = ?, color = ?, year = ?, license_plate = ?, vin = ?, seats = ?, transmission_type = ?, fuel_type = ?, availability = ? 
                WHERE id = ?";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute([
            $car->getBrand(),
            $car->getColor(),
            $car->getYear(),
            $car->getLicensePlate(),
            $car->getVin(),
            $car->getSeats(),
            $car->getTransmissionType(),
            $car->getFuelType(),
            $car->getAvailability(),
            $car->getId()
        ]);
    }
}