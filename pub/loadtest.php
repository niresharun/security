<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '5G');
error_reporting(E_ALL);


define('DS', DIRECTORY_SEPARATOR);
require __DIR__ . '/../app/bootstrap.php';
use Magento\Framework\App\Bootstrap;

$noOfCustomer = 600;
$designerTable = 'grandriver_cc_designers_loadtest';
$passwordStr = 'newuser@123';

$params = filter_input_array(INPUT_POST);
$bootstrap = Bootstrap::create(BP, []);
$obj = $bootstrap->getObjectManager();
$resourceConnection = $obj->get('Magento\Framework\App\ResourceConnection');
$connection = $resourceConnection->getConnection();

/**  @var CustomerInterfaceFactory $customerFactory */
$customerFactory = $obj->get("\Magento\Customer\Api\Data\CustomerInterfaceFactory");
/**  @var CustomerRepositoryInterface $customerRepository */
$customerRepository = $obj->get("\Magento\Customer\Api\CustomerRepositoryInterface");

/**  @var AddressInterfaceFactory $addressFactory */
$addressFactory = $obj->get("\Magento\Customer\Api\Data\AddressInterfaceFactory");
/**  @var AddressRepositoryInterface $addressRepository */
$addressRepository = $obj->get("\Magento\Customer\Api\AddressRepositoryInterface");

/**  @var Encryptor $encryptor */
$encryptor = $obj->get("\Magento\Framework\Encryption\Encryptor");

TestCompDataEntry($noOfCustomer, $connection, $designerTable);

CreateCustomer($connection, $designerTable, $customerFactory, $customerRepository, $encryptor, $passwordStr, $addressFactory, $addressRepository);

CleanData($connection, $designerTable);

/**================== Data entry in table: grandriver_cc_designers_loadtest ============*/
function TestCompDataEntry($noOfCustomer, $connection, $table) {
    $compInc = 1;
    for( $i = 1; $i <= $noOfCustomer; $i++ ) {
        if($i > 1) {
            $compInc ++;
        }
        if($i > 1 && $i % 2 == 0) {
            $compInc = round($i/2);
        }
        $companyName = 'Perficient LoadTest'.$compInc;
        $customerData = [
            'customer_id' => $i,
            'email' => 'loadtest'.$i.'@perficient.com',
            'import_status' => 0,
            'company_name' => $companyName,
            'telephone' => '123-456-7890',
            'address_line_1' => '123 Highland Avenue',
            'city' => 'Milwaukee',
            'state' => 64,
            'postal_code' => 53201
        ];
        $connection->insert($table, $customerData);
    }
}


function CreateCustomer($connection, $designerTable, $customerFactory, $customerRepository, $encryptor, $passwordStr, $addressFactory, $addressRepository )
{
    $sql = "SELECT * FROM ".$designerTable." where import_status = 0";
    $records = $connection->fetchAll($sql);
    $i = 1;
    foreach ($records as $record ) {
        $street = [];
        $email = $record['email'];
        $firstName = 'TestUser'.$i;
        $lastName = 'LastName'.$i;
        $customer = $customerFactory->create();

        $customer->setWebsiteId(1);
        $customer->setEmail($email);
        $customer->setFirstname($firstName);
        $customer->setLastname($lastName);
        //$customer->setResaleCertificateNumber('123456');
        //$customer->setBusinessType('Designer');
        //$customer->setNoOfStores(2);
        //$customer->setNoOfStores(2);


        $passwordHash = $encryptor->getHash($passwordStr, true);
        $customer = $customerRepository->save($customer, $passwordHash);

        /* save address as customer */
        $address = $addressFactory->create();
        $address->setFirstname($firstName);
        $address->setLastname($lastName);
        $address->setTelephone('7789654567');

        $street[] = '139 ALTAMA CONNECTOR';//pass street as array
        $address->setStreet($street);

        $address->setCity('Brunswick');
        $address->setCountryId('US');
        $address->setPostcode('95120');
        $address->setRegionId(19);
        $address->setIsDefaultShipping(1);
        $address->setIsDefaultBilling(1);
        $address->setCustomerId($customer->getId());
        $addressRepository->save($address);

        $i++;

        $dataBind = ['customer_id' => $customer->getId()];
        $connection->update($designerTable, $dataBind, array('email = ?' => $email));

    }
}

function CleanData($connection, $designerTable)
{
    /************* Clean Street ********************/
    $sql = 'UPDATE '.$designerTable.' SET cleaned_street = SUBSTRING(REPLACE(LOWER(TRIM(address_line_1)), " ", ""), 1, 10)
WHERE address_line_1 IS NOT NULL';
    $connection->query($sql);

    /************* Clean Company Name ********************/
    $sql = 'UPDATE '.$designerTable.' SET cleaned_company_name = LOWER(TRIM(company_name))
WHERE company_name IS NOT NULL';
    $connection->query($sql);

    /************* Clean Telephone ********************/
    $sql = "UPDATE ".$designerTable." SET cleaned_telephone = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(telephone,'-',''), '.',''), '(',''), ')',''), '/',''), ' ',''), '+',''), '*',''), '{',''), '}','')";
    $connection->query($sql);

    /************* Company Name Count ********************/
    $sql = "UPDATE ".$designerTable." AS `dest`,
            (
                SELECT cleaned_company_name, COUNT(cleaned_company_name) AS cnt
                FROM ".$designerTable."
                GROUP BY cleaned_company_name
            ) AS `src`
            SET `dest`.`company_name_count` = `src`.`cnt`
            WHERE `dest`.cleaned_company_name = `src`.cleaned_company_name";
    $connection->query($sql);
}






