<?php

namespace Purdia\Party\Application\DTOs;

final readonly class CreatePersonDTO
{
    public function __construct(
        public string $firstName,
        public ?string $lastName = null,
        public ?string $code = null,
        public ?string $birthDate = null,
        public ?string $gender = null,
        public ?string $religion = null,
        public ?string $bloodType = null,
        public ?string $maritalStatus = null,
        public ?string $nationalId = null,
        public ?array $contacts = null,
        public ?array $addresses = null,
        public ?array $roles = null,
    ) {}
}
