<?php

namespace Pim\Bundle\EnrichBundle\Normalizer;

use Pim\Bundle\EnrichBundle\Provider\Form\FormProviderInterface;
use Pim\Bundle\VersioningBundle\Manager\VersionManager;
use Pim\Component\Catalog\Localization\Localizer\AttributeConverterInterface;
use Pim\Component\Catalog\Model\ProductModelInterface;
use Pim\Component\Catalog\Model\ValueInterface;
use Pim\Component\Catalog\Repository\LocaleRepositoryInterface;
use Pim\Component\Enrich\Converter\ConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\scalar;

/**
 * @author    Adrien Pétremann <adrien.petremann@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class ProductModelNormalizer implements NormalizerInterface
{
    /** @var string[] */
    private $supportedFormat = ['internal_api'];

    /** @var NormalizerInterface */
    private $normalizer;

    /** @var NormalizerInterface */
    private $versionNormalizer;

    /** @var NormalizerInterface */
    private $fileNormalizer;

    /** @var VersionManager */
    private $versionManager;

    /** @var AttributeConverterInterface */
    private $localizedConverter;

    /** @var ConverterInterface */
    private $productValueConverter;

    /** @var FormProviderInterface */
    private $formProvider;

    /** @var LocaleRepositoryInterface */
    private $localeRepository;

    /**
     * @param NormalizerInterface         $normalizer
     * @param NormalizerInterface         $versionNormalizer
     * @param NormalizerInterface              $fileNormalizer
     * @param VersionManager              $versionManager
     * @param AttributeConverterInterface $localizedConverter
     * @param ConverterInterface          $productValueConverter
     * @param FormProviderInterface       $formProvider
     * @param LocaleRepositoryInterface   $localeRepository
     */
    public function __construct(
        NormalizerInterface $normalizer,
        NormalizerInterface $versionNormalizer,
        NormalizerInterface $fileNormalizer,
        VersionManager $versionManager,
        AttributeConverterInterface $localizedConverter,
        ConverterInterface $productValueConverter,
        FormProviderInterface $formProvider,
        LocaleRepositoryInterface $localeRepository
    ) {
        $this->normalizer            = $normalizer;
        $this->versionNormalizer     = $versionNormalizer;
        $this->fileNormalizer        = $fileNormalizer;
        $this->versionManager        = $versionManager;
        $this->localizedConverter    = $localizedConverter;
        $this->productValueConverter = $productValueConverter;
        $this->formProvider          = $formProvider;
        $this->localeRepository      = $localeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($productModel, $format = null, array $context = []): array
    {
        $normalizedProductModel = $this->normalizer->normalize($productModel, 'standard', $context);
        $normalizedProductModel['values'] = $this->localizedConverter->convertToLocalizedFormats(
            $normalizedProductModel['values'],
            $context
        );

        $normalizedProductModel['family'] = $productModel->getFamilyVariant()->getFamily()->getCode();
        $normalizedProductModel['values'] = $this->productValueConverter->convert($normalizedProductModel['values']);

        $oldestLog = $this->versionManager->getOldestLogEntry($productModel);
        $newestLog = $this->versionManager->getNewestLogEntry($productModel);

        $created = null !== $oldestLog ? $this->versionNormalizer->normalize($oldestLog, 'internal_api') : null;
        $updated = null !== $newestLog ? $this->versionNormalizer->normalize($newestLog, 'internal_api') : null;

        $normalizedFamilyVariant = $this->normalizer->normalize($productModel->getFamilyVariant(), 'standard');

        $normalizedProductModel['meta'] = [
                'family_variant' => $normalizedFamilyVariant,
                'form'           => $this->formProvider->getForm($productModel),
                'id'             => $productModel->getId(),
                'created'        => $created,
                'updated'        => $updated,
                'model_type'     => 'product_model',
                'image'          => $this->normalizeImage($productModel->getImage(), $format, $context),
            ] + $this->getLabels($productModel);

        return $normalizedProductModel;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ProductModelInterface && in_array($format, $this->supportedFormat);
    }

    /**
     * @param ProductModelInterface $productModel
     *
     * @return array
     */
    private function getLabels(ProductModelInterface $productModel): array
    {
        $labels = [];

        foreach ($this->localeRepository->getActivatedLocaleCodes() as $localeCode) {
            $labels[$localeCode] = $productModel->getLabel($localeCode);
        }

        return ['label' => $labels];
    }

    /**
     * @param ValueInterface|null $data
     * @param string              $format
     * @param array               $context
     *
     * @return array|null
     */
    private function normalizeImage(?ValueInterface $data, string $format, array $context = []): ?array
    {
        if (null === $data) {
            return null;
        }

        return $this->fileNormalizer->normalize($data->getData(), $format, $context);
    }
}
