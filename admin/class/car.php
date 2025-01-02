<?php


class Car {
    private $id;
    private $brand;
    private $model;
    private $price;
    private $available;
    private $color;
    private $year;
    private $license_plate;
    private $vin;
    private $seats;
    private $transmission_type;
    private $fuel_type;
    private $availability;
/*
    public function __construct($id, $brand, $color, $year, $license_plate,
        $vin, $seats, $transmission_type, $fuel_type, $availability) {
        $this->id = $id;
        $this->brand = $brand;
        $this->color = $color;
        $this->year = $year;
        $this->license_plate = $license_plate;
        $this->vin = $vin;
        $this->seats = $seats;
        $this->transmission_type = $transmission_type;
        $this->fuel_type = $fuel_type;
        $this->availability = $availability;
    }
*/


    public function __construct($id, $model, $brand, $price, $available)
    {
       $this->id = $id;
       $this->brand = $brand;
       $this->price = $price;
       $this->model = $model;
       $this->available = $available;
       
       
    }
    // Getters
    public function getId() { 
        return $this->id;
    }
    public function getModel(){
        return $this->model;
    }
    public function getPrice(){
        return $this->price;
    }
    public function getAvailable(){
        return $this->available;
    }

    public function getBrand() {
        return $this->brand;
    }

    public function getColor() {
        return $this->color;
    }

    public function getYear() {
        return $this->year;
    }

    public function getLicensePlate() {
        return $this->license_plate;
    }

    public function getVin() {
        return $this->vin;
    }

    public function getSeats() {
        return $this->seats;
    }

    public function getTransmissionType() {
        return $this->transmission_type;
    }

    public function getFuelType() {
        return $this->fuel_type;
    }

    public function getAvailability() {
        return $this->availability;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setBrand($brand) {
        $this->brand = $brand;
    }

    public function setColor($color) {
        $this->color = $color;
    }

    public function setYear($year) {
        $this->year = $year;
    }

    public function setLicensePlate($license_plate) {
        $this->license_plate = $license_plate;
    }

    public function setVin($vin) {
        $this->vin = $vin;
    }

    public function setSeats($seats) {
        $this->seats = $seats;
    }

    public function setTransmissionType($transmission_type) {
        $this->transmission_type = $transmission_type;
    }

    public function setFuelType($fuel_type) {
        $this->fuel_type = $fuel_type;
    }

    public function setAvailability($availability) {
        $this->availability = $availability;
    }
    public function setPrice($price) {
        $this->price = $price;
    }
    public function setAvailable($available) {
        $this->available = $available;

    }
    public function setModel($model) {
        $this->model = $model;

    }
}