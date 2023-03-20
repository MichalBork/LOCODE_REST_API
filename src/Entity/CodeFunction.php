<?php

namespace App\Entity;

use App\DTO\CodeFunctionDTO;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'code_function', options: ['collate' => 'utf8mb4_unicode_ci', 'charset' => 'utf8mb4'])]
class CodeFunction
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;


    #[ORM\Column(type: 'boolean', length: 1, nullable: false)]
    private bool $unknown;

    #[ORM\Column(type: 'boolean', length: 1, nullable: false)]
    private bool $defined;

    #[ORM\Column(type: 'boolean', length: 1, nullable: false)]
    private bool $railTerminal;

    #[ORM\Column(type: 'boolean', length: 1, nullable: false)]
    private bool $roadTerminal;

    #[ORM\Column(type: 'boolean', length: 1, nullable: false)]
    private bool $airport;

    #[ORM\Column(type: 'boolean', length: 1, nullable: false)]
    private bool $postalExchangeOffice;

    #[ORM\Column(type: 'boolean', length: 1, nullable: false)]
    private bool $icd;

    #[ORM\Column(type: 'boolean', length: 1, nullable: false)]
    private bool $fixTransportFacility;

    #[ORM\Column(type: 'boolean', length: 1, nullable: false)]
    private bool $borderCrossing;


    #[ORM\OneToOne(targetEntity: Locode::class, inversedBy: 'codeFunction', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Locode $locode;


    public function __construct(
        bool $unknown,
        bool $defined,
        bool $railTerminal,
        bool $roadTerminal,
        bool $airport,
        bool $postalExchangeOffice,
        bool $icd,
        bool $fixTransportFacility,
        bool $borderCrossing,
        Locode $locode
    ) {
        $this->unknown = $unknown;
        $this->defined = $defined;
        $this->railTerminal = $railTerminal;
        $this->roadTerminal = $roadTerminal;
        $this->airport = $airport;
        $this->postalExchangeOffice = $postalExchangeOffice;
        $this->icd = $icd;
        $this->fixTransportFacility = $fixTransportFacility;
        $this->borderCrossing = $borderCrossing;
        $this->locode = $locode;
    }


    public static function createFromCodeFunctionDTO(CodeFunctionDTO $codeFunctionDTO): self
    {

        return new self(

            $codeFunctionDTO->isUnknown(),
            $codeFunctionDTO->isDefined(),
            $codeFunctionDTO->isRailTerminal(),
            $codeFunctionDTO->isRoadTerminal(),
            $codeFunctionDTO->isAirport(),
            $codeFunctionDTO->isPostalExchangeOffice(),
            $codeFunctionDTO->isIcd(),
            $codeFunctionDTO->isFixTransportFacility(),
            $codeFunctionDTO->isBorderCrossing(),
            $codeFunctionDTO->getLocode()

        );
    }

}