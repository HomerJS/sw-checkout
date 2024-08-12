<?php declare(strict_types=1);

namespace Ihor\CheckOut\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1723479069 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1723479069;
    }
//create custom document
    public function update(Connection $connection): void
    {
        $documentConfigId = Uuid::randomBytes();
        $documentTypeId = $this->getDocumentTypeId($connection);

        $connection->insert('document_base_config', [
            'id' => $documentConfigId,
            'name' => 'custom',
            'filename_prefix' => 'custom_',
            'global' => 0,
            'document_type_id' => $documentTypeId,
            'config' => $this->getConfig(),
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
        ]);

        $storefrontSalesChannelId = $this->getStorefrontSalesChannelId($connection);
        if (!$storefrontSalesChannelId) {
            return;
        }

        $connection->insert('document_base_config_sales_channel', [
            'id' => Uuid::randomBytes(),
            'document_base_config_id' => $documentConfigId,
            'sales_channel_id' => $storefrontSalesChannelId,
            'document_type_id' => $documentTypeId,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
        ]);
    }

    public function updateDestructive(Connection $connection): void
    {
    }

    private function getDocumentTypeId(Connection $connection): string
    {
        $sql = <<<SQL
            SELECT id
            FROM document_type
            WHERE technical_name = "delivery_note"
SQL;
        return $connection->fetchOne($sql);
    }

    private function getConfig(): string
    {
        $config = [
            'displayPrices' => false,
            'displayFooter' => true,
            'displayHeader' => true,
            'displayLineItems' => true,
            'displayLineItemPosition' => true,
            'displayPageCount' => true,
            'displayCompanyAddress' => true,
            'pageOrientation' => 'portrait',
            'pageSize' => 'a4',
            'itemsPerPage' => 10,
            'companyName' => 'Example company',
            'companyAddress' => 'Example company address',
            'companyEmail' => 'custom@example.org'
        ];

        return json_encode($config);
    }

    private function getStorefrontSalesChannelId(Connection $connection): ?string
    {
        $sql = <<<SQL
            SELECT id
            FROM sales_channel
            WHERE type_id = :typeId
SQL;
        $salesChannelId = $connection->fetchOne($sql, [
            'typeId' => Uuid::fromHexToBytes(Defaults::SALES_CHANNEL_TYPE_STOREFRONT)
        ]);

        if (!$salesChannelId) {
            return null;
        }

        return $salesChannelId;
    }
}
