<?php

class Car {
    private $car_id;
    private $car_description;
    private $car_brand;
    private $car_model;
    private $car_year;
    private $car_type;
    private $car_color;
    private $car_seats;
    private $car_transmission_type;
    private $car_fuel_type;
    private $car_rental_rate;
    private $car_excess_per_hour;
    private $car_availability;

    public function __construct($car_id, $car_model, $car_brand, $car_availability) {
        $this->car_id = $car_id;
        $this->car_brand = $car_brand;
        $this->car_model = $car_model;
        $this->car_availability = $car_availability;
    }

    // Getters
    public function getId() { 
        return $this->car_id;
    }
    public function getDescription() { 
        return $this->car_description;
    }
    public function getBrand() {
        return $this->car_brand;
    }
    public function getModel() {
        return $this->car_model;
    }
    public function getYear() {
        return $this->car_year;
    }
    public function getType() {
        return $this->car_type;
    }
    public function getColor() {
        return $this->car_color;
    }
    public function getSeats() {
        return $this->car_seats;
    }
    public function getTransmissionType() {
        return $this->car_transmission_type;
    }
    public function getFuelType() {
        return $this->car_fuel_type;
    }
    public function getRentalRate() {
        return $this->car_rental_rate;
    }
    public function getExcessPerHour() {
        return $this->car_excess_per_hour;
    }
    public function getAvailability() {
        return $this->car_availability;
    }

    // Setters
    public function setId($car_id) {
        $this->car_id = $car_id;
    }
    public function setDescription($car_description) {
        $this->car_description = $car_description;
    }
    public function setBrand($car_brand) {
        $this->car_brand = $car_brand;
    }
    public function setModel($car_model) {
        $this->car_model = $car_model;
    }
    public function setYear($car_year) {
        $this->car_year = $car_year;
    }
    public function setType($car_type) {
        $this->car_type = $car_type;
    }
    public function setColor($car_color) {
        $this->car_color = $car_color;
    }
    public function setSeats($car_seats) {
        $this->car_seats = $car_seats;
    }
    public function setTransmissionType($car_transmission_type) {
        $this->car_transmission_type = $car_transmission_type;
    }
    public function setFuelType($car_fuel_type) {
        $this->car_fuel_type = $car_fuel_type;
    }
    public function setRentalRate($car_rental_rate) {
        $this->car_rental_rate = $car_rental_rate;
    }
    public function setExcessPerHour($car_excess_per_hour) {    
        $this->car_excess_per_hour = $car_excess_per_hour;
    }
    public function setAvailability($car_availability) {
        $this->car_availability = $car_availability;
    }
}
