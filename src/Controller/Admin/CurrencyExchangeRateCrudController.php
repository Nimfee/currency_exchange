<?php

namespace App\Controller\Admin;

use App\Entity\CurrencyExchangeRate;
use App\Service\Currency\CurrencyService;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class CurrencyExchangeRateCrudController extends AbstractCrudController
{
    /** @var CurrencyService  */
    protected $currencyService;
    
    public function __construct(CurrencyService $currencyService )
    {
        $this->currencyService = $currencyService;
    }

    public static function getEntityFqcn(): string
    {
        return CurrencyExchangeRate::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $currencies = $this->currencyService->getFormattedCurrencies();

        return [
            ChoiceField::new('currencyFromId')->setChoices($currencies),
            ChoiceField::new('currencyToId')->setChoices($currencies),
            NumberField::new('rate'),
            DateField::new('rateDate')
        ];
    }
}
