<?php

namespace App\Controller\Admin;

use App\Entity\Source;
use App\Service\Currency\CurrencyService;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Routing\Annotation\Route;

class SourceCrudController extends AbstractCrudController
{
    /** @var CurrencyService  */
    protected $currencyService;

    public function __construct(CurrencyService $currencyService )
    {
        $this->currencyService = $currencyService;
    }

    /**
     * @Route("/admin/source", name="admin")
     */
    public static function getEntityFqcn(): string
    {
        return Source::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $currencies = $this->currencyService->getFormattedCurrencies();

        return [
            TextField::new('name'),
            UrlField::new('url'),
            ChoiceField::new('currencyId')->setChoices($currencies)
        ];
    }
}
