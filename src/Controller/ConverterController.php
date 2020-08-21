<?php
namespace App\Controller;

use App\Entity\CurrencyExchangeRate;
use App\Entity\Currency;
use App\Repository\CurrencyExchangeRateRepository;
use App\Repository\CurrencyRepository;
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

    public function __construct(
        CurrencyRepository $currencyRepository,
        CurrencyExchangeRateRepository $currencyExchangeRepository
    )
    {
        $this->currencyRepository = $currencyRepository;
        $this->currencyExchangeRepository = $currencyExchangeRepository;
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
            'result' => $this->processResult(
                $data['currencyFrom'],
                $data['currencyTo'],
                $data['conversionAmount'],
                $currencyExchangeRepository
            )
        ]);
    }

    /**
     * @param int $from
     * @param int $to
     * @param float $value
     * @return float
     */
    protected function processResult(int $from, int $to, float $value): float
    {
        $exchangeRate = null;
        $exchangeRate = $this->currencyExchangeRepository->findCurrencyExchangeRate($from, $to);
        if (null !== $exchangeRate) {
            return $this->getValueByRate($from, $exchangeRate, $value);
        }
        $commonCurrency = $this->currencyExchangeRepository->findComplexCurrencyExchangeRate($from, $to);

        if (count($commonCurrency) > 0) {
            $ids = array_shift($commonCurrency);
            if (is_array($ids)) {
                /** @var CurrencyExchangeRate $exchangeRate */
                $exchangeRateFrom = $this->currencyExchangeRepository->findCurrencyExchangeRate(
                    $ids['from'], $ids['to']
                );

                $value = $this->getValueByRate($ids['from'], $exchangeRateFrom, $value);
            } else {
                $ids = ['from' => $ids, 'to' => $ids];
            }
            /** @var CurrencyExchangeRate $exchangeRate */
            $exchangeRateFrom = $this->currencyExchangeRepository->findCurrencyExchangeRate($from, $ids['from']);

            $value = null !== $exchangeRateFrom ? $this->getValueByRate($from, $exchangeRateFrom, $value) : 0;

            $exchangeRateTo = $this->currencyExchangeRepository->findCurrencyExchangeRate($to,  $ids['to']);

            return  null !== $exchangeRateTo ? $this->getValueByRate($ids['to'], $exchangeRateTo, $value) : 0;
        }

        return 0;
    }

    /**
     * @param int $currency
     * @param CurrencyExchangeRate $currencyExchangeRate
     * @param float $value
     * @return float
     */
    protected function getValueByRate(
        int $currency,
        CurrencyExchangeRate $currencyExchangeRate,
        float $value
    )
    {
        return $currencyExchangeRate->getCurrencyFrom()->getId() == $currency ?
            $value * $currencyExchangeRate->getRate() : $value / $currencyExchangeRate->getRate();
    }
}
