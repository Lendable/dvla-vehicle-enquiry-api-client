<?php

declare(strict_types=1);

namespace Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\Response;

use Assert\Assert;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject\Date;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject\MotStatus;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject\RegistrationNumber;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject\TaxStatus;
use Lendable\Dvla\VehicleEnquiry\Scope\VehiclesScope\ValueObject\YearAndMonth;

class EnquiryResponse
{
    private RegistrationNumber $registrationNumber;

    private ?TaxStatus $taxStatus;

    private ?Date $taxDueDate;

    private ?MotStatus $motStatus;

    private ?Date $motExpiryDate;

    private ?string $make;

    private ?YearAndMonth $monthOfFirstDvlaRegistration;

    private ?YearAndMonth $monthOfFirstRegistration;

    private ?int $yearOfManufacture;

    private ?int $engineCapacity;

    private ?int $co2Emissions;

    private ?string $fuelType;

    private ?bool $markedForExport;

    private ?string $colour;

    private ?string $typeApproval;

    private ?string $wheelplan;

    private ?int $revenueWeight;

    private ?string $realDrivingEmissions;

    private ?Date $dateOfLastV5CIssued;

    private ?string $euroStatus;

    private function __construct()
    {
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromArray(array $data): self
    {
        Assert::that($data)
            ->keyExists('registrationNumber');

        $instance = new self();
        $instance->registrationNumber = RegistrationNumber::fromString($data['registrationNumber']);
        $instance->taxStatus = isset($data['taxStatus']) ? TaxStatus::fromString($data['taxStatus']) : null;
        $instance->taxDueDate = isset($data['taxDueDate']) ? Date::fromString($data['taxDueDate']) : null;
        $instance->motStatus = isset($data['motStatus']) ? MotStatus::fromString($data['motStatus']) : null;
        $instance->motExpiryDate = isset($data['motExpiryDate']) ? Date::fromString($data['motExpiryDate']) : null;
        $instance->make = $data['make'] ?? null;
        $instance->monthOfFirstDvlaRegistration = isset($data['monthOfFirstDvlaRegistration']) ? YearAndMonth::fromString($data['monthOfFirstDvlaRegistration']) : null;
        $instance->monthOfFirstRegistration = isset($data['monthOfFirstRegistration']) ? YearAndMonth::fromString($data['monthOfFirstRegistration']) : null;
        $instance->yearOfManufacture = $data['yearOfManufacture'] ?? null;
        $instance->engineCapacity = $data['engineCapacity'] ?? null;
        $instance->co2Emissions = $data['co2Emissions'] ?? null;
        $instance->fuelType = $data['fuelType'] ?? null;
        $instance->markedForExport = $data['markedForExport'] ?? null;
        $instance->colour = $data['colour'] ?? null;
        $instance->typeApproval = $data['typeApproval'] ?? null;
        $instance->wheelplan = $data['wheelplan'] ?? null;
        $instance->revenueWeight = $data['revenueWeight'] ?? null;
        $instance->realDrivingEmissions = $data['realDrivingEmissions'] ?? null;
        $instance->dateOfLastV5CIssued = isset($data['dateOfLastV5CIssued']) ? Date::fromString($data['dateOfLastV5CIssued']) : null;
        $instance->euroStatus = $data['euroStatus'] ?? null;

        return $instance;
    }

    public function getRegistrationNumber(): RegistrationNumber
    {
        return $this->registrationNumber;
    }

    public function getTaxStatus(): ?TaxStatus
    {
        return $this->taxStatus;
    }

    public function getTaxDueDate(): ?Date
    {
        return $this->taxDueDate;
    }

    public function getMotStatus(): ?MotStatus
    {
        return $this->motStatus;
    }

    public function getMotExpiryDate(): ?Date
    {
        return $this->motExpiryDate;
    }

    public function getMake(): ?string
    {
        return $this->make;
    }

    public function getMonthOfFirstDvlaRegistration(): ?YearAndMonth
    {
        return $this->monthOfFirstDvlaRegistration;
    }

    public function getMonthOfFirstRegistration(): ?YearAndMonth
    {
        return $this->monthOfFirstRegistration;
    }

    public function getYearOfManufacture(): ?int
    {
        return $this->yearOfManufacture;
    }

    public function getEngineCapacity(): ?int
    {
        return $this->engineCapacity;
    }

    public function getCo2Emissions(): ?int
    {
        return $this->co2Emissions;
    }

    public function getFuelType(): ?string
    {
        return $this->fuelType;
    }

    public function getMarkedForExport(): ?bool
    {
        return $this->markedForExport;
    }

    public function getColour(): ?string
    {
        return $this->colour;
    }

    public function getTypeApproval(): ?string
    {
        return $this->typeApproval;
    }

    public function getWheelplan(): ?string
    {
        return $this->wheelplan;
    }

    public function getRevenueWeight(): ?int
    {
        return $this->revenueWeight;
    }

    public function getRealDrivingEmissions(): ?string
    {
        return $this->realDrivingEmissions;
    }

    public function getDateOfLastV5CIssued(): ?Date
    {
        return $this->dateOfLastV5CIssued;
    }

    public function getEuroStatus(): ?string
    {
        return $this->euroStatus;
    }
}
