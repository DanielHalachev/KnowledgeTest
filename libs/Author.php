<?php

class Author implements JsonSerializable {

    private int $id;
    private string $facultyNumber;
    private ?string $firstName;
    private ?string $lastName;

    public function __construct(int $id, string $facultyNumber, ?string $firstName, ?string $lastName) {
        $this->id = $id;
        $this->facultyNumber = $facultyNumber;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'facultyNumber' => $this->facultyNumber,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
        ];
    }

    public function getId(): int {
        return $this->id;
    }

    public function getFacultyNumber(): string {
        return $this->facultyNumber;
    }

    public function getFirstName(): ?string {
        return $this->firstName;
    }

    public function getLastName(): ?string {
        return $this->lastName;
    }
}