<?php
namespace App\Controller;

use App\Entity\CurrencyExchangeRate;
use App\Entity\Currency;
use App\Repository\CurrencyExchangeRateRepository;
use App\Repository\CurrencyRepository;
use App\Service\CurrencyExchange\CurrencyExchangeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Webmozart\Assert\Assert as AssertAssert;

/**
 * @Route("/converter")
 */
class ConverterController extends AbstractController
{
    protected $currencyRepository = null;
    protected $currencyExchangeRepository = null;
    protected $currencyExchangeManager = null;

    public function __construct(
        CurrencyRepository $currencyRepository,
        CurrencyExchangeRateRepository $currencyExchangeRepository,
        CurrencyExchangeManager $currencyExchangeManager
    )
    {
        $this->currencyRepository = $currencyRepository;
        $this->currencyExchangeRepository = $currencyExchangeRepository;
        $this->currencyExchangeManager = $currencyExchangeManager;
    }

    /**
     * @Route("/", name="converter_index", methods={"GET"})
     */
    public function index(CurrencyRepository $currencyRepository): Response
    {
        $currencies = $this->currencyRepository->getAllInArray();

        return $this->render('converter/index.html.twig', [
            'currencies' => $currencies,
        ]);
    }

    /**
     * @Route("/convert", name="converter_execute", methods={"POST"})
     */
    public function convert(Request $request, CurrencyExchangeRateRepository $currencyExchangeRepository): Response
    {
        $validator = Validation::createValidator();
        $data = json_decode($request->getContent(), true);

        $input = [
            'currencyTo' => $data['currencyTo'],
            'conversionAmount' => (float) $data['conversionAmount']
        ];

        $constraints = new Assert\Collection([
            'currencyTo' => [new Assert\NotEqualTo($data['currencyFrom'])],
            'conversionAmount' => [new Assert\NotBlank(), new Assert\Type("float")],
        ]);

        $violations = $validator->validate($input, $constraints);

        if (count($violations) > 0) {

            $accessor = PropertyAccess::createPropertyAccessor();

            $errorMessages = [];

            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages,
                    $violation->getPropertyPath(),
                    $violation->getMessage()
                );
            }

            if (array_key_exists('currencyTo', $errorMessages)) {
                $errorMessages['currencyTo'] = 'Currency to should not be equal to Currency from';
            }

            return $this->json([
                'errorMessages' => $errorMessages,
            ]);
        }

        return $this->json([
            'result' => $this->currencyExchangeManager->processResult(
                $data['currencyFrom'],
                $data['currencyTo'],
                (float) $data['conversionAmount'],
                $currencyExchangeRepository
            )
        ]);
    }
}
