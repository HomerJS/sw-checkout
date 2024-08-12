<?php declare(strict_types=1);

namespace Ihor\CheckOut\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Migration\Traits\ImportTranslationsTrait;
use Shopware\Core\Migration\Traits\Translations;

class Migration1723480309 extends MigrationStep
{
    use ImportTranslationsTrait;

    final public const TYPE = 'example';

    public function getCreationTimestamp(): int
    {
        return 1723480309;
    }

    //create document type
    public function update(Connection $connection): void
    {
        $documentTypeId = Uuid::randomBytes();

        $connection->insert('document_type', [
            'id' => $documentTypeId,
            'technical_name' => self::TYPE,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
        ]);

        $this->addTranslations($connection, $documentTypeId);
        $this->addDocumentBaseConfig($connection, $documentTypeId);
    }

    public function updateDestructive(Connection $connection): void
    {
    }

    private function addTranslations(Connection $connection, string $documentTypeId): void
    {
        $englishName = 'Example document type name';
        $germanName = 'Beispiel Dokumententyp Name';

        $documentTypeTranslations = new Translations(
            [
                'document_type_id' => $documentTypeId,
                'name' => $germanName,
            ],
            [
                'document_type_id' => $documentTypeId,
                'name' => $englishName,
            ]
        );

        $this->importTranslation(
            'document_type_translation',
            $documentTypeTranslations,
            $connection
        );
    }

    private function addDocumentBaseConfig(Connection $connection, string $documentTypeId): void
    {
        $defaultConfig = [
            'displayPrices' => true,
            'displayFooter' => true,
            'displayHeader' => true,
            'displayLineItems' => true,
            'diplayLineItemPosition' => true,
            'displayPageCount' => true,
            'displayCompanyAddress' => true,
            'pageOrientation' => 'portrait',
            'pageSize' => 'a4',
            'itemsPerPage' => 10,
            'companyName' => 'Example Company',
            'taxNumber' => '',
            'vatId' => '',
            'taxOffice' => '',
            'bankName' => '',
            'bankIban' => '',
            'bankBic' => '',
            'placeOfJurisdiction' => '',
            'placeOfFulfillment' => '',
            'executiveDirector' => '',
            'companyAddress' => '',
            'referencedDocumentType' => self::TYPE,
        ];

        $documentBaseConfigId = Uuid::randomBytes();

        $connection->insert(
            'document_base_config',
            [
                'id' => $documentBaseConfigId,
                'name' => self::TYPE,
                'global' => 1,
                'filename_prefix' => self::TYPE . '_',
                'document_type_id' => $documentTypeId,
                'config' => json_encode($defaultConfig, \JSON_THROW_ON_ERROR),
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]
        );
        $connection->insert(
            'document_base_config_sales_channel',
            [
                'id' => Uuid::randomBytes(),
                'document_base_config_id' => $documentBaseConfigId,
                'document_type_id' => $documentTypeId,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]
        );
    }
}
