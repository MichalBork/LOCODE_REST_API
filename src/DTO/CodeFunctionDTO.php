<?php

namespace App\DTO;

use App\Entity\Locode;

class CodeFunctionDTO
{

    private bool $unknown;
    private bool $defined;
    private bool $railTerminal;
    private bool $roadTerminal;
    private bool $airport;
    private bool $postalExchangeOffice;
    private bool $icd;
    private bool $fixTransportFacility;
    private bool $borderCrossing;
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


    public static function createFromArray(array $function, Locode $locode): self
    {
        return new self(
            false,
            $function[0],
            $function[1],
            $function[2],
            $function[3],
            $function[4],
            $function[5],
            $function[6],
            $function[7],
            $locode

        );
    }


    public static function createCodeFunctionWithUnknown(Locode $locode): self
    {
        return new self(
            true,
            false,
            false,
            false,
            false,
            false,
            false,
            false,
            false,
            $locode
        );
    }

    /**
     * @return Locode
     */
    public function getLocode(): Locode
    {
        return $this->locode;
    }




    /**
     * @return bool
     */
    public function isUnknown(): bool
    {
        return $this->unknown;
    }

    /**
     * @return bool
     */
    public function isDefined(): bool
    {
        return $this->defined;
    }

    /**
     * @return bool
     */
    public function isRailTerminal(): bool
    {
        return $this->railTerminal;
    }

    /**
     * @return bool
     */
    public function isRoadTerminal(): bool
    {
        return $this->roadTerminal;
    }

    /**
     * @return bool
     */
    public function isAirport(): bool
    {
        return $this->airport;
    }

    /**
     * @return bool
     */
    public function isPostalExchangeOffice(): bool
    {
        return $this->postalExchangeOffice;
    }

    /**
     * @return bool
     */
    public function isIcd(): bool
    {
        return $this->icd;
    }

    /**
     * @return bool
     */
    public function isFixTransportFacility(): bool
    {
        return $this->fixTransportFacility;
    }

    /**
     * @return bool
     */
    public function isBorderCrossing(): bool
    {
        return $this->borderCrossing;
    }


}