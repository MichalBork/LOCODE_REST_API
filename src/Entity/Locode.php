<?php

namespace App\Entity;

use App\DTO\CodeFunctionDTO;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'locode', options: ['collate' => 'utf8mb4_unicode_ci', 'charset' => 'utf8mb4'])]
class Locode
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 1, nullable: false)]
    private $changeIndicator;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private $locode;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $name;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private $nameWoDiacritics;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $subdivision;

    #[ORM\OneToOne(targetEntity: CodeFunction::class, inversedBy: 'locode', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private CodeFunction $codeFunction;


    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $status;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $date;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $iata;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $coordinates;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $remarks;


    public function __construct(
        ?string $changeIndicator,
        string $locode,
        string $name,
        ?string $nameWoDiacritics,
        string $subdivision,
        string $status,
        string $date,
        string $iata,
        string $coordinates,
        string $remarks
    ) {
        $this->changeIndicator = $changeIndicator;
        $this->locode = $locode;
        $this->name = $name;
        $this->nameWoDiacritics = $nameWoDiacritics;
        $this->subdivision = $subdivision;
        $this->status = $status;
        $this->date = $date;
        $this->iata = $iata;
        $this->coordinates = $coordinates;
        $this->remarks = $remarks;
    }


    public function toArray(): array
    {
        return [
            'changeIndicator' => $this->changeIndicator,
            'locode' => $this->locode,
            'name' => $this->name,
            'nameWoDiacritics' => $this->nameWoDiacritics,
            'subdivision' => $this->subdivision,

            'status' => $this->status,
            'date' => $this->date,
            'iata' => $this->iata,
            'coordinates' => $this->coordinates,
            'remarks' => $this->remarks,
            'function' => $this->codeFunction->toArray(),
        ];
    }

    public function addCodeFunction(CodeFunctionDTO $codeFunction): void
    {
        $this->codeFunction = CodeFunction::createFromCodeFunctionDTO($codeFunction) ;
    }

    /**
     * @param string|null $changeIndicator
     */
    public function setChangeIndicator(?string $changeIndicator): void
    {
        $this->changeIndicator = $changeIndicator;
    }

    /**
     * @param string $locode
     */
    public function setLocode(string $locode): void
    {
        $this->locode = $locode;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string|null $nameWoDiacritics
     */
    public function setNameWoDiacritics(?string $nameWoDiacritics): void
    {
        $this->nameWoDiacritics = $nameWoDiacritics;
    }

    /**
     * @param string $subdivision
     */
    public function setSubdivision(string $subdivision): void
    {
        $this->subdivision = $subdivision;
    }

    /**
     * @param string $function
     */
    public function setFunction(string $function): void
    {
        $this->function = $function;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @param string $date
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    /**
     * @param string $iata
     */
    public function setIata(string $iata): void
    {
        $this->iata = $iata;
    }

    /**
     * @param string $coordinates
     */
    public function setCoordinates(string $coordinates): void
    {
        $this->coordinates = $coordinates;
    }

    /**
     * @param string $remarks
     */
    public function setRemarks(string $remarks): void
    {
        $this->remarks = $remarks;
    }





}